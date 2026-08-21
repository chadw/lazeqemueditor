<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Achievement extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'achievements';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
        'description',
        'icon_id',
        'points',
        'has_reward',
        'client_flag',
        'version',
        'reset_on_version_change',
        'enabled',
    ];

    protected $casts = [
        'has_reward' => 'boolean',
        'reset_on_version_change' => 'boolean',
        'enabled' => 'boolean',
    ];

    public function categoryAssociations(): HasMany
    {
        return $this->hasMany(AchievementCategoryAssociation::class, 'achievement_id', 'id')
            ->orderBy('sequence')
            ->orderBy('category_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            AchievementCategory::class,
            'achievement_category_associations',
            'achievement_id',
            'category_id'
        )
            ->withPivot(['sequence', 'display_text'])
            ->orderByPivot('sequence')
            ->orderBy('achievement_categories.id');
    }

    public function components(): HasMany
    {
        return $this->hasMany(AchievementComponent::class, 'achievement_id', 'id')
            ->orderBy('component_type')
            ->orderBy('sequence')
            ->orderBy('component_id');
    }

    public function criteria(): HasMany
    {
        return $this->hasMany(AchievementCriterion::class, 'achievement_id', 'id')
            ->orderBy('component_type')
            ->orderBy('component_sequence')
            ->orderBy('id');
    }

    public function automaticRewardEntries(): HasMany
    {
        return $this->hasMany(RewardSourceEntry::class, 'source_id', 'id')
            ->where('source_type', RewardSource::ACHIEVEMENT)
            ->orderBy('sequence')
            ->orderBy('reward_id');
    }

    public function rewardSource(): HasOne
    {
        return $this->hasOne(RewardSource::class, 'source_id', 'id')
            ->where('source_type', RewardSource::ACHIEVEMENT);
    }

    public function castRequirements(): HasMany
    {
        return $this->hasMany(AchievementCastRestriction::class, 'achievement_id', 'id')
            ->orderBy('restriction_id');
    }

    public function characterCompletions(): HasMany
    {
        return $this->hasMany(CharacterAchievement::class, 'achievement_id', 'id');
    }

    public function characterProgress(): HasMany
    {
        return $this->hasMany(CharacterAchievementProgress::class, 'achievement_id', 'id');
    }

    public function characterRewards(): HasMany
    {
        return $this->hasMany(CharacterAchievementReward::class, 'achievement_id', 'id');
    }

    public function characterRewardSelections(): HasMany
    {
        return $this->hasMany(CharacterAchievementRewardSelection::class, 'achievement_id', 'id');
    }

    public function pendingUpdates(): HasMany
    {
        return $this->hasMany(CharacterAchievementPendingUpdate::class, 'achievement_id', 'id');
    }
}
