<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterAchievementPendingMutation extends BaseModel
{
    protected $connection = 'eqemu';

    protected $table = 'character_achievement_pending_mutations';

    protected $primaryKey = 'mutation_id';

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'character_id',
        'source_target_type',
        'source_target_id',
        'operation',
        'achievement_id',
        'component_type',
        'component_id',
        'requested_value',
        'definition_version',
        'status',
        'attempt_count',
        'created_at',
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

    public function componentForIdentity(): Builder
    {
        return AchievementComponent::query()
            ->where('achievement_id', $this->achievement_id)
            ->where('component_type', $this->component_type)
            ->where('component_id', $this->component_id);
    }
}
