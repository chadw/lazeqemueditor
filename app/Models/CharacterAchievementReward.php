<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterAchievementReward extends BaseModel
{
    protected $connection = 'eqemu';

    protected $table = 'character_achievement_rewards';

    protected $primaryKey = null;

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'character_id',
        'achievement_id',
        'reward_id',
        'status',
        'attempt_count',
        'granted_at',
        'last_attempt_at',
        'last_error',
    ];

    public function character(): BelongsTo
    {
        return $this->belongsTo(CharacterData::class, 'character_id', 'id');
    }

    public function achievement(): BelongsTo
    {
        return $this->belongsTo(Achievement::class, 'achievement_id', 'id');
    }

    public function reward(): BelongsTo
    {
        return $this->belongsTo(AchievementReward::class, 'reward_id', 'reward_id');
    }

    public function getKey()
    {
        return "{$this->character_id}:{$this->achievement_id}:{$this->reward_id}";
    }

    public function getKeyName()
    {
        return 'character_id';
    }

    protected function setKeysForSaveQuery($query)
    {
        return $query
            ->where('character_id', $this->getAttribute('character_id'))
            ->where('achievement_id', $this->getAttribute('achievement_id'))
            ->where('reward_id', $this->getAttribute('reward_id'));
    }

    protected function setKeysForSelectQuery($query)
    {
        return $query
            ->where('character_id', $this->getAttribute('character_id'))
            ->where('achievement_id', $this->getAttribute('achievement_id'))
            ->where('reward_id', $this->getAttribute('reward_id'));
    }
}
