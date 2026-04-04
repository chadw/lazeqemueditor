<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoottableEntry extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'loottable_entries';
    public $incrementing = false;
    protected $primaryKey = null;
    public $timestamps = false;

    protected $fillable = [
        'loottable_id',
        'lootdrop_id',
        'multiplier',
        'droplimit',
        'mindrop',
        'probability',
    ];

    public function loottable(): BelongsTo
    {
        return $this->belongsTo(LootTable::class, 'loottable_id', 'id');
    }

    public function npcs(): HasMany
    {
        return $this->hasMany(NpcType::class, 'loottable_id', 'loottable_id');
    }

    public function lootdropEntries(): HasMany
    {
        return $this->hasMany(LootdropEntry::class, 'lootdrop_id', 'lootdrop_id')
            ->orderBy('chance');
    }

    public function lootdrop(): BelongsTo
    {
        return $this->belongsTo(LootDrop::class, 'lootdrop_id', 'id');
    }
}
