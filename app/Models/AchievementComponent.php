<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AchievementComponent extends BaseModel
{
    protected $connection = 'eqemu';

    protected $table = 'achievement_components';

    protected $primaryKey = null;

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'achievement_id',
        'component_type',
        'sequence',
        'component_id',
        'description',
        'description_2',
    ];

    public function achievement(): BelongsTo
    {
        return $this->belongsTo(Achievement::class, 'achievement_id', 'id');
    }

    public function componentCount(): BelongsTo
    {
        return $this->belongsTo(AchievementComponentCount::class, 'component_id', 'component_id');
    }

    /**
     * Exact criterion query for this component's three-column identity.
     *
     * Laravel does not natively support eager-loaded composite relationships,
     * so callers should use this query or group Achievement::criteria by the
     * same identity instead of joining on component_id alone.
     */
    public function criteriaForIdentity(): Builder
    {
        return AchievementCriterion::query()
            ->where('achievement_id', $this->achievement_id)
            ->where('component_type', $this->component_type)
            ->where('component_id', $this->component_id)
            ->orderBy('id');
    }

    public function getKey()
    {
        return "{$this->achievement_id}:{$this->component_type}:{$this->component_id}";
    }

    public function getKeyName()
    {
        return 'achievement_id';
    }

    protected function setKeysForSaveQuery($query)
    {
        return $query
            ->where('achievement_id', $this->getAttribute('achievement_id'))
            ->where('component_type', $this->getAttribute('component_type'))
            ->where('component_id', $this->getAttribute('component_id'));
    }

    protected function setKeysForSelectQuery($query)
    {
        return $query
            ->where('achievement_id', $this->getAttribute('achievement_id'))
            ->where('component_type', $this->getAttribute('component_type'))
            ->where('component_id', $this->getAttribute('component_id'));
    }
}
