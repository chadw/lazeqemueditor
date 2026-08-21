<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class AchievementReward extends BaseModel
{
    protected $connection = 'eqemu';

    protected $table = 'rewards';

    protected $primaryKey = 'reward_id';

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'reward_type',
        'reward_data_id',
        'amount',
        'description',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public function optionEntries(): HasMany
    {
        return $this->hasMany(AchievementRewardOptionEntry::class, 'reward_id', 'reward_id');
    }

    public function sourceEntries(): HasMany
    {
        return $this->hasMany(RewardSourceEntry::class, 'reward_id', 'reward_id');
    }

    public function characterRewards(): HasMany
    {
        return $this->hasMany(CharacterAchievementReward::class, 'reward_id', 'reward_id');
    }
}
