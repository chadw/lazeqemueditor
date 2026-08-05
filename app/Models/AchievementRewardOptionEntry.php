<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AchievementRewardOptionEntry extends BaseModel
{
    protected $connection = 'eqemu';

    protected $table = 'achievement_reward_option_entries';

    protected $primaryKey = null;

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'reward_set_id',
        'option_id',
        'reward_id',
    ];

    public function rewardSet(): BelongsTo
    {
        return $this->belongsTo(AchievementRewardSet::class, 'reward_set_id', 'reward_set_id');
    }

    public function reward(): BelongsTo
    {
        return $this->belongsTo(AchievementReward::class, 'reward_id', 'reward_id');
    }

    /**
     * Exact option query for this entry's two-column option identity.
     */
    public function optionForIdentity(): Builder
    {
        return AchievementRewardOption::query()
            ->where('reward_set_id', $this->reward_set_id)
            ->where('option_id', $this->option_id);
    }

    public function getKey()
    {
        return "{$this->reward_set_id}:{$this->option_id}:{$this->reward_id}";
    }

    public function getKeyName()
    {
        return 'reward_set_id';
    }

    protected function setKeysForSaveQuery($query)
    {
        return $query
            ->where('reward_set_id', $this->getAttribute('reward_set_id'))
            ->where('option_id', $this->getAttribute('option_id'))
            ->where('reward_id', $this->getAttribute('reward_id'));
    }

    protected function setKeysForSelectQuery($query)
    {
        return $query
            ->where('reward_set_id', $this->getAttribute('reward_set_id'))
            ->where('option_id', $this->getAttribute('option_id'))
            ->where('reward_id', $this->getAttribute('reward_id'));
    }
}
