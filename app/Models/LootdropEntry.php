<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class LootdropEntry extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'lootdrop_entries';
    public $incrementing = false;
    protected $primaryKey = null;
    public $timestamps = false;

    protected $fillable = [
        'lootdrop_id',
        'item_id',
        'item_charges',
        'equip_item',
        'chance',
        'disabled_chance',
        'trivial_min_level',
        'trivial_max_level',
        'multiplier',
        'npc_min_level',
        'npc_max_level',
        'min_expansion',
        'max_expansion',
        'content_flags',
        'content_flags_disabled',
    ];

    protected $casts = [
        'equip_item' => 'boolean',
    ];

    public function lootdrop(): BelongsTo
    {
        return $this->belongsTo(LootDrop::class, 'lootdrop_id', 'id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id', 'id')
            ->select('id', 'Name', 'icon');
    }

    public function npcs(): HasManyThrough
    {
        return $this->hasManyThrough(
            NpcType::class,
            LoottableEntry::class,
            'lootdrop_id',
            'loottable_id',
            'lootdrop_id',
            'loottable_id'
        );
    }
}
