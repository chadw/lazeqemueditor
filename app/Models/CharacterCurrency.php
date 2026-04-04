<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterCurrency extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'character_currency';
    public $timestamps = false;

    public function character(): BelongsTo
    {
        return $this->belongsTo(CharacterData::class, 'id');
    }
}
