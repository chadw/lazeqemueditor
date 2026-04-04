<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterInventory extends BaseModel
{
    protected $connection = 'eqemu';
    protected $primaryKey = 'slot_id';
    protected $table = 'inventory';

    public function character(): BelongsTo
    {
        return $this->belongsTo(CharacterData::class, 'character_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function aug1(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'augment_one', 'id');
    }

    public function aug2(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'augment_two', 'id');
    }

    public function aug3(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'augment_three', 'id');
    }

    public function aug4(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'augment_four', 'id');
    }

    public function aug5(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'augment_five', 'id');
    }

    public function aug6(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'augment_six', 'id');
    }
}
