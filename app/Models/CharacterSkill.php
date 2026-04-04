<?php

namespace App\Models;

class CharacterSkill extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'character_skills';

    public function character()
    {
        return $this->belongsTo(CharacterData::class, 'id');
    }
}
