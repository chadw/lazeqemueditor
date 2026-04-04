<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class NpcFaction extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'npc_faction';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'primaryfaction',
        'ignore_primary_assist',
    ];

    protected $casts = [
        'ignore_primary_assist' => 'boolean',
    ];

    public function npcs(): HasMany
    {
        return $this->hasMany(NpcType::class, 'npc_faction_id', 'id');
    }

    public function faction(): BelongsTo
    {
        return $this->belongsTo(FactionList::class, 'primaryfaction', 'id');
    }

    public function entries(): HasMany
    {
        return $this->hasMany(NpcFactionEntry::class, 'npc_faction_id', 'id');
    }

    public static function options(): Collection
    {
        return self::select('id', 'name')
            ->orderBy('name')
            ->groupBy('id')
            ->get();
    }
}
