<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CharacterParcel extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'character_parcels';
    public $timestamps = false;

    protected $fillable = [
        'char_id',
        'item_id',
        'aug_slot_1',
        'aug_slot_2',
        'aug_slot_3',
        'aug_slot_4',
        'aug_slot_5',
        'aug_slot_6',
        'quantity',
        'evolve_amount',
        'from_name',
        'note',
        'sent_date',
        'slot_id',
    ];

    protected $casts = [
        'sent_date' => 'datetime',
    ];

    public function character(): BelongsTo
    {
        return $this->belongsTo(CharacterData::class, 'char_id')
            ->select('id', 'name');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id')
            ->select('id', 'Name', 'icon');
    }

    public function container(): HasMany
    {
        return $this->hasMany(CharacterParcelContainer::class, 'parcels_id', 'id');
    }

    public function aug1(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'aug_slot_1')
            ->select('id', 'Name', 'icon');
    }

    public function aug2(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'aug_slot_2')
            ->select('id', 'Name', 'icon');
    }

    public function aug3(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'aug_slot_3')
            ->select('id', 'Name', 'icon');
    }

    public function aug4(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'aug_slot_4')
            ->select('id', 'Name', 'icon');
    }

    public function aug5(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'aug_slot_5')
            ->select('id', 'Name', 'icon');
    }

    public function aug6(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'aug_slot_6')
            ->select('id', 'Name', 'icon');
    }
}
