<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterBandolier extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'character_bandolier';
    public $timestamps = false;

    public function character(): BelongsTo
    {
        return $this->belongsTo(CharacterData::class, 'id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id')
            ->select('id', 'Name', 'icon');
    }
}
