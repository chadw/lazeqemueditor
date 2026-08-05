<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AchievementCriterion extends BaseModel
{
    protected $connection = 'eqemu';

    protected $table = 'achievement_criteria';

    public $timestamps = false;

    protected $fillable = [
        'achievement_id',
        'component_type',
        'component_sequence',
        'component_id',
        'event_type',
        'progress_mode',
        'behavior',
        'target_id',
        'target_id2',
        'target_value',
        'required_count',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public function achievement(): BelongsTo
    {
        return $this->belongsTo(Achievement::class, 'achievement_id', 'id');
    }

    /**
     * Exact component query for the criterion's three-column component identity.
     */
    public function componentForIdentity(): Builder
    {
        return AchievementComponent::query()
            ->where('achievement_id', $this->achievement_id)
            ->where('component_type', $this->component_type)
            ->where('component_id', $this->component_id);
    }
}
