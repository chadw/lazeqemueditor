<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AchievementRewardOption extends BaseModel
{
    protected $connection = 'eqemu';

    protected $table = 'achievement_reward_options';

    protected $primaryKey = null;

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'reward_set_id',
        'option_id',
        'sequence',
        'label',
        'common_to_all',
        'flags',
        'enabled',
    ];

    protected $casts = [
        'common_to_all' => 'boolean',
        'enabled' => 'boolean',
    ];

    public function rewardSet(): BelongsTo
    {
        return $this->belongsTo(AchievementRewardSet::class, 'reward_set_id', 'reward_set_id');
    }

    /**
     * Exact entry query for this option's two-column identity.
     *
     * Option IDs are intentionally reusable across reward sets, so joining on
     * option_id alone is unsafe.
     */
    public function entriesForIdentity(): Builder
    {
        return AchievementRewardOptionEntry::query()
            ->where('reward_set_id', $this->reward_set_id)
            ->where('option_id', $this->option_id)
            ->orderBy('reward_id');
    }

    public function getKey()
    {
        return "{$this->reward_set_id}:{$this->option_id}";
    }

    public function getKeyName()
    {
        return 'reward_set_id';
    }

    protected function setKeysForSaveQuery($query)
    {
        return $query
            ->where('reward_set_id', $this->getAttribute('reward_set_id'))
            ->where('option_id', $this->getAttribute('option_id'));
    }

    protected function setKeysForSelectQuery($query)
    {
        return $query
            ->where('reward_set_id', $this->getAttribute('reward_set_id'))
            ->where('option_id', $this->getAttribute('option_id'));
    }
}
