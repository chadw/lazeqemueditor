<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class AchievementRewardSet extends BaseModel
{
    protected $connection = 'eqemu';

    protected $table = 'reward_sets';

    protected $primaryKey = 'reward_set_id';

    public $incrementing = false;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'reward_set_id',
        'title',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public function options(): HasMany
    {
        return $this->hasMany(AchievementRewardOption::class, 'reward_set_id', 'reward_set_id')
            ->orderBy('sequence')
            ->orderBy('option_id');
    }

    public function optionEntries(): HasMany
    {
        return $this->hasMany(AchievementRewardOptionEntry::class, 'reward_set_id', 'reward_set_id')
            ->orderBy('option_id')
            ->orderBy('reward_id');
    }

    public function characterSelections(): HasMany
    {
        return $this->hasMany(
            CharacterAchievementRewardSelection::class,
            'reward_set_id',
            'reward_set_id'
        );
    }

    public function sources(): HasMany
    {
        return $this->hasMany(RewardSource::class, 'reward_set_id', 'reward_set_id');
    }
}
