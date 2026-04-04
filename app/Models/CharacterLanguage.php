<?php

namespace App\Models;

class CharacterLanguage extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'character_languages';

    public function character()
    {
        return $this->belongsTo(CharacterData::class, 'id');
    }
}
