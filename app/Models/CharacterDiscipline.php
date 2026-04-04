<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterDiscipline extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'character_disciplines';
    public $timestamps = false;

    public function character(): BelongsTo
    {
        return $this->belongsTo(CharacterData::class, 'id');
    }

    public function spell(): BelongsTo
    {
        return $this->belongsTo(Spell::class, 'disc_id')
            ->select('id', 'name', 'new_icon', 'targettype');
    }
}
