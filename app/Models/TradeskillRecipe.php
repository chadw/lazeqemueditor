<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Kyslik\ColumnSortable\Sortable;

class TradeskillRecipe extends BaseModel
{
    use Sortable;

    protected $connection = 'eqemu';
    protected $table = 'tradeskill_recipe';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'tradeskill',
        'skillneeded',
        'trivial',
        'nofail',
        'replace_container',
        'notes',
        'must_learn',
        'learned_by_item_id',
        'quest',
        'enabled',
        'min_expansion',
        'max_expansion',
        'content_flags',
        'content_flags_disabled',
    ];

    public array $sortable = [
        'id',
        'name',
        'tradeskill',
        'skillneeded',
        'trivial',
    ];

    protected $casts = [
        'nofail'            => 'boolean',
        'replace_container' => 'boolean',
        'quest'             => 'boolean',
        'enabled'           => 'boolean',
    ];

    public function getLearnFlagsAttribute(): array
    {
        $value = (int) $this->must_learn;

        $flags = [
            'l_method'  => 0,
            'l_message' => 0,
            'l_search'  => 0,
        ];

        if ($value >= 32) {
            $flags['l_search'] = 32;
            $value -= 32;
        }

        if ($value >= 16) {
            $flags['l_message'] = 16;
            $value -= 16;
        }

        $flags['l_method'] = $value;

        return $flags;
    }

    public static function buildLearnValue(int $method, int $message, int $search): int
    {
        return $method + $message + $search;
    }

    public function learnedByItem(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'learned_by_item_id', 'id')
            ->select('id', 'Name', 'icon');
    }

    public function entries(): HasMany
    {
        return $this->hasMany(TradeskillRecipeEntry::class, 'recipe_id');
    }

    public function containerEntries()
    {
        return $this->hasMany(TradeskillRecipeEntry::class, 'recipe_id')
            ->where('iscontainer', 1)
            ->with('item');
        //return $this->entries()->with('item')->where('iscontainer', 1)->get();
    }

    public function successEntries()
    {
        return $this->entries()->with('item')->where('successcount', '>', 0)->get();
    }

    public function resultEntries(): HasMany
    {
        return $this->hasMany(TradeskillRecipeEntry::class, 'recipe_id')
            ->where('successcount', '>', 0)
            ->with('item');
    }

    public function failEntries()
    {
        return $this->entries()->with('item')->where('failcount', '>', 0)->get();
    }

    public function componentEntriesWithFlags()
    {
        $components = $this->entries()
            ->with('item')
            ->where('iscontainer', 0)
            ->where('componentcount', '>', 0)
            ->get();

        foreach ($components as $component) {
            $itemId = $component->item?->id;

            $component->custom_is_merchant = \App\Models\Merchantlist::where('item', $itemId)->exists();
            $component->custom_is_drop = \App\Models\LootdropEntry::where('item_id', $itemId)->exists();
            $component->custom_is_foraged = \App\Models\Forage::where('itemid', $itemId)->exists();
            $component->custom_is_fished = \App\Models\Fishing::where('itemid', $itemId)->exists();

            $component->subcombine_recipe = \App\Models\TradeskillRecipe::findByProduct($itemId);
        }

        return $components;
    }

    public static function findByProduct(?int $itemId): ?self
    {
        if ($itemId === null) {
            return null;
        }

        return self::whereHas('entries', function ($q) use ($itemId) {
            $q->where('successcount', '>', 0)
                ->where('item_id', $itemId);
        })->first();
    }

    public static function findByProductWithComponents(?int $itemId): ?self
    {
        if ($itemId === null) {
            return null;
        }

        $recipe = self::whereHas('entries', function ($q) use ($itemId) {
            $q->where('successcount', '>', 0)
                ->where('item_id', $itemId);
        })->first();

        if ($recipe) {
            $recipe->setRelation('components', $recipe->componentEntriesWithFlags());
        }

        return $recipe;
    }

    public function resolvedContainerEntries(): Collection
    {
        $objects = collect(config('everquest.tradeskill_containers'))->keyBy('id');

        return $this->containerEntries->map(function ($entry) use ($objects) {
            if ($entry->item) {
                $entry->container_id = $entry->item->id;
                $entry->container_name = $entry->item->Name;
                $entry->container_icon = $entry->item->icon ?? null;
                $entry->is_object_container = false;

                return $entry;
            }

            if ($objects->has($entry->item_id)) {
                $object = $objects->get($entry->item_id);
                $entry->container_id = $object['id'];
                $entry->container_name = $object['name'];
                $entry->container_icon = $object['icon'];
                $entry->is_object_container = true;
            }

            return $entry;
        });
    }

    public function cloneWithEntries(): self
    {
        return DB::transaction(function () {

            $clone = $this->replicate();
            $clone->name = $this->name . ' (Copy)';
            $clone->save();

            $entries = $this->entries->map(function ($entry) use ($clone) {
                $copy = $entry->replicate();
                $copy->recipe_id = $clone->id;
                return $copy;
            });

            $clone->entries()->saveMany($entries);

            return $clone;
        });
    }

    protected static function booted()
    {
        static::deleting(function ($recipe) {
            $recipe->entries()->delete();
        });
    }

    /**
     * tradeskillSortable
     *
     * @param  mixed $query
     * @param  mixed $direction
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function tradeskillSortable($query, $direction)
    {
        $tradeskills = collect(config('everquest.skills.tradeskill'));

        $cases = "CASE tradeskill ";
        foreach ($tradeskills as $id => $name) {
            $id = (int) $id;
            $safe = str_replace("'", "\\'", $name);
            $cases .= "WHEN {$id} THEN '{$safe}' ";
        }
        $cases .= "ELSE tradeskill END";

        $query->select($this->getTable() . '.*')
            ->orderByRaw("{$cases} {$direction}");

        return $query;
    }
}
