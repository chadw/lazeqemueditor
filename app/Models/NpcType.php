<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Kyslik\ColumnSortable\Sortable;

class NpcType extends BaseModel
{
    use Sortable;

    protected $connection = 'eqemu';
    protected $table = 'npc_types';
    public $timestamps = false;
    protected $guarded = [];

    protected $casts = [
        'see_invis' => 'boolean',
        'see_invis_undead' => 'boolean',
        'see_hide' => 'boolean',
        'see_improved_hide' => 'boolean',
        'qglobal' => 'boolean', // deprecated
        'npc_aggro' => 'boolean',
        'findable' => 'boolean',
        'trackable' => 'boolean',
        'isbot' => 'boolean',
        'exclude' => 'boolean',
        'private_corpse' => 'boolean',
        'unique_spawn_by_name' => 'boolean',
        'underwater' => 'boolean',
        'isquest' => 'boolean',
        'no_target_hotkey' => 'boolean',
        'raid_target' => 'boolean',
        'ignore_despawn' => 'boolean',
        'show_name' => 'boolean',
        'untargetable' => 'boolean',
        'skip_global_loot' => 'boolean',
        'rare_spawn' => 'boolean',
        'always_aggro' => 'boolean',
        'keeps_sold_items' => 'boolean',
        'is_parcel_merchant' => 'boolean',
        'multiquest_enabled' => 'boolean',
    ];

    public array $sortable = [
        'id',
        'name',
        'alt_currency_id'
    ];

    public function getCleanNameAttribute(): string
    {
        return $this->npcFixName($this->name);
    }

    public function getParsedSpecialAbilitiesAttribute()
    {
        $raw = $this->special_abilities;
        if (!$raw) {
            return [];
        }

        $attacks = explode('^', $raw);
        $labels = [];

        foreach ($attacks as $entry) {
            $parts = explode(',', $entry);
            $id = intval($parts[0]);
            $label = config('everquest.special_attacks.' . $id);

            if ($label) {
                $labels[] = $label;
            }
        }

        return $labels;
    }

    public static function getForZone(Zone $zone, int $version = 0, bool $merchantsOnly = false): Collection
    {
        $zoneShort = $zone->short_name;
        $zoneId = $zone->zoneidnumber;

        $selects = [
            'id',
            'class',
            'hp',
            'level',
            'trackable',
            'maxlevel',
            'race',
            'name',
            'loottable_id',
            'raid_target',
            'rare_spawn',
            'special_abilities',
            'merchant_id'
        ];

        $query1 = self::whereHas('spawnEntries.spawn2', function ($query) use ($zoneShort, $version) {
            $query->where('zone', $zoneShort)
                ->when($version > 0, fn($q) => $q->where('version', $version));
        })
            ->when($merchantsOnly, fn($q) => $q->where('merchant_id', '>', 0))
            ->select($selects)
            ->get();

        $query2 = self::select($selects)
            ->when($merchantsOnly, fn($q) => $q->where('merchant_id', '>', 0))
            ->whereRaw('CAST(SUBSTRING(id, 1, LENGTH(id) - 3) AS UNSIGNED) = ?', [$zoneId])
            ->whereDoesntHave('spawnEntries')
            ->get();

        return $query1
            ->merge($query2)
            ->unique('id')
            ->sortBy(fn($npc) => $npc->clean_name)
            ->values();
    }

    public function firstSpawnEntries(): HasOne
    {
        return $this->hasOne(SpawnEntry::class, 'npcID', 'id')
            ->whereHas('spawn2')
            ->orderBy('spawngroupID');
    }

    public function spawnEntries(): HasMany
    {
        return $this->hasMany(SpawnEntry::class, 'npcID', 'id');
    }

    public function lootTable(): HasOne
    {
        return $this->hasOne(LootTable::class, 'id', 'loottable_id');
    }

    public function loottableEntries(): HasMany
    {
        return $this->hasMany(LoottableEntry::class, 'loottable_id', 'loottable_id');
    }

    public function merchantlist(): HasMany
    {
        return $this->hasMany(Merchantlist::class, 'merchantid', 'merchant_id');
    }

    public function npcSpellset(): BelongsTo
    {
        return $this->belongsTo(NpcSpell::class, 'npc_spells_id', 'id')
            ->select('id', 'name', 'parent_list', 'attack_proc', 'proc_chance');
    }

    public function primaryFaction()
    {
        return $this->belongsTo(NpcFaction::class, 'npc_faction_id', 'id');
    }

    public function factionEntries()
    {
        return $this->hasMany(NpcFactionEntry::class, 'npc_faction_id', 'npc_faction_id')
            ->where('npc_faction_id', '>', 0);
    }

    public function lootDrops(): HasManyThrough
    {
        return $this->hasManyThrough(
            LootdropEntry::class,
            LoottableEntry::class,
            'loottable_id',
            'lootdrop_id',
            'loottable_id',
            'lootdrop_id'
        );
    }

    public static function npcFixName(string $npc): string
    {
        $name = str_replace(['#', '!', '~'], '', $npc);
        $name = str_replace('_', ' ', $name);
        $name = str_replace('-', '`', $name);
        $name = preg_replace('/\d/', '', $name);

        return $name;
    }

    public function otherCountUsing(string $column): int
    {
        if (!$this->{$column}) {
            return 0;
        }

        return static::where($column, $this->{$column})
            ->whereKeyNot($this->id)
            ->count();
    }
}
