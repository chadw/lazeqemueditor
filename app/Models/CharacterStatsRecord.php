<?php

namespace App\Models;

class CharacterStatsRecord extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'character_stats_record';

    public function character()
    {
        return $this->belongsTo(CharacterData::class, 'id');
    }
}
