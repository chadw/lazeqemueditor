<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AchievementRewardSet extends BaseModel
{
    protected $connection = 'eqemu';

    protected $table = 'achievement_reward_sets';

    protected $primaryKey = 'reward_set_id';

    public $incrementing = false;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'reward_set_id',
        'achievement_id',
        'title',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public function achievement(): BelongsTo
    {
        return $this->belongsTo(Achievement::class, 'achievement_id', 'id');
    }

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
}
