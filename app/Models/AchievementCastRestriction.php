<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AchievementCastRestriction extends BaseModel
{
    protected $connection = 'eqemu';

    protected $table = 'achievement_cast_restrictions';

    protected $primaryKey = null;

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'restriction_id',
        'achievement_id',
        'requires_completed',
    ];

    protected $casts = [
        'requires_completed' => 'boolean',
    ];

    public function achievement(): BelongsTo
    {
        return $this->belongsTo(Achievement::class, 'achievement_id', 'id');
    }

    public function getKey()
    {
        return "{$this->restriction_id}:{$this->achievement_id}";
    }

    public function getKeyName()
    {
        return 'restriction_id';
    }

    protected function setKeysForSaveQuery($query)
    {
        return $query
            ->where('restriction_id', $this->getAttribute('restriction_id'))
            ->where('achievement_id', $this->getAttribute('achievement_id'));
    }

    protected function setKeysForSelectQuery($query)
    {
        return $query
            ->where('restriction_id', $this->getAttribute('restriction_id'))
            ->where('achievement_id', $this->getAttribute('achievement_id'));
    }
}
