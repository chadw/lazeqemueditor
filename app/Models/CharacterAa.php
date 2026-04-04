<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterAa extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'character_alternate_abilities';

    public function character(): BelongsTo
    {
        return $this->belongsTo(CharacterData::class, 'id');
    }
}
