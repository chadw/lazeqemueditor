<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AchievementReward extends BaseModel
{
    protected $connection = 'eqemu';

    protected $table = 'achievement_rewards';

    protected $primaryKey = 'reward_id';

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'achievement_id',
        'sequence',
        'reward_type',
        'reward_data_id',
        'amount',
        'description',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public function achievement(): BelongsTo
    {
        return $this->belongsTo(Achievement::class, 'achievement_id', 'id');
    }

    public function optionEntry(): HasOne
    {
        return $this->hasOne(AchievementRewardOptionEntry::class, 'reward_id', 'reward_id');
    }

    public function characterRewards(): HasMany
    {
        return $this->hasMany(CharacterAchievementReward::class, 'reward_id', 'reward_id');
    }
}
