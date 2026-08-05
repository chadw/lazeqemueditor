<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterAchievementRewardSelection extends BaseModel
{
    protected $connection = 'eqemu';

    protected $table = 'character_achievement_reward_selections';

    protected $primaryKey = null;

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'character_id',
        'achievement_id',
        'reward_set_id',
        'selected_option_id',
        'status',
        'attempt_count',
        'claimed_at',
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

    public function rewardSet(): BelongsTo
    {
        return $this->belongsTo(AchievementRewardSet::class, 'reward_set_id', 'reward_set_id');
    }

    public function selectedOptionForIdentity(): Builder
    {
        return AchievementRewardOption::query()
            ->where('reward_set_id', $this->reward_set_id)
            ->where('option_id', $this->selected_option_id);
    }

    public function getKey()
    {
        return "{$this->character_id}:{$this->achievement_id}:{$this->reward_set_id}";
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
            ->where('reward_set_id', $this->getAttribute('reward_set_id'));
    }

    protected function setKeysForSelectQuery($query)
    {
        return $query
            ->where('character_id', $this->getAttribute('character_id'))
            ->where('achievement_id', $this->getAttribute('achievement_id'))
            ->where('reward_set_id', $this->getAttribute('reward_set_id'));
    }
}
