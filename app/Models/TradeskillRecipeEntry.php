<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TradeskillRecipeEntry extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'tradeskill_recipe_entries';
    public $timestamps = false;

    protected $fillable = [
        'recipe_id',
        'item_id',
        'successcount',
        'failcount',
        'componentcount',
        'salvagecount',
        'iscontainer',
    ];

    protected $casts = [
        'iscontainer' => 'boolean',
    ];

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(TradeskillRecipe::class, 'recipe_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id')
            ->select('id', 'Name', 'icon');
    }

    public function getResolvedItemNameAttribute()
    {
        if ($this->item) {
            return $this->item->Name;
        }

        if ($this->is_object_container) {
            $container = collect(config('everquest.tradeskill_containers'))
                ->firstWhere('id', $this->container_id);

            return $container['name'] ?? 'Unknown Container';
        }

        return 'Unknown Item';
    }

    public function getResolvedItemIconAttribute()
    {
        if ($this->item) {
            return $this->item->icon;
        }

        if ($this->is_object_container) {
            $container = collect(config('everquest.tradeskill_containers'))
                ->firstWhere('id', $this->container_id);

            return $container['icon'] ?? $this->container_icon;
        }

        return null;
    }
}
