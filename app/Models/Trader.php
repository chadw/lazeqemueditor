<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Trader extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'trader';
    public $timestamps = false;

    public function character(): BelongsTo
    {
        return $this->belongsTo(CharacterData::class, 'char_id')
            ->select(['id', 'name']);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id')
            ->select('id', 'Name', 'icon');
    }
}
