<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterAchievementProgress extends BaseModel
{
    protected $connection = 'eqemu';

    protected $table = 'character_achievement_progress';

    protected $primaryKey = null;

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'character_id',
        'achievement_id',
        'component_type',
        'component_sequence',
        'component_id',
        'current_count',
        'completed',
        'version',
        'updated_at',
    ];

    protected $casts = [
        'completed' => 'boolean',
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

    public function getKey()
    {
        return "{$this->character_id}:{$this->achievement_id}:{$this->component_type}:{$this->component_id}";
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
            ->where('component_type', $this->getAttribute('component_type'))
            ->where('component_id', $this->getAttribute('component_id'));
    }

    protected function setKeysForSelectQuery($query)
    {
        return $query
            ->where('character_id', $this->getAttribute('character_id'))
            ->where('achievement_id', $this->getAttribute('achievement_id'))
            ->where('component_type', $this->getAttribute('component_type'))
            ->where('component_id', $this->getAttribute('component_id'));
    }
}
