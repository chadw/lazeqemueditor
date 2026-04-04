<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterTribute extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'character_tribute';
    public $timestamps = false;

    public function character(): BelongsTo
    {
        return $this->belongsTo(CharacterData::class, 'character_id');
    }

    public function _tribute(): BelongsTo
    {
        return $this->belongsTo(Tribute::class, 'tribute');
    }
}
