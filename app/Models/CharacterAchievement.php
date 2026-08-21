<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterAchievement extends BaseModel
{
    protected $connection = 'eqemu';

    protected $table = 'character_achievements';

    protected $primaryKey = null;

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'character_id',
        'achievement_id',
        'version',
        'completed_at',
    ];

    public function character(): BelongsTo
    {
        return $this->belongsTo(CharacterData::class, 'character_id', 'id');
    }

    public function achievement(): BelongsTo
    {
        return $this->belongsTo(Achievement::class, 'achievement_id', 'id');
    }

    public function getKey()
    {
        return "{$this->character_id}:{$this->achievement_id}";
    }

    public function getKeyName()
    {
        return 'character_id';
    }

    protected function setKeysForSaveQuery($query)
    {
        return $query
            ->where('character_id', $this->getAttribute('character_id'))
            ->where('achievement_id', $this->getAttribute('achievement_id'));
    }

    protected function setKeysForSelectQuery($query)
    {
        return $query
            ->where('character_id', $this->getAttribute('character_id'))
            ->where('achievement_id', $this->getAttribute('achievement_id'));
    }
}
