<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RewardSource extends BaseModel
{
    public const ACHIEVEMENT = 1;

    public const TASK = 2;

    public const GENERAL = 3;

    protected $connection = 'eqemu';

    protected $table = 'reward_sources';

    protected $primaryKey = null;

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'source_type',
        'source_id',
        'reward_set_id',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public function rewardSet(): BelongsTo
    {
        return $this->belongsTo(AchievementRewardSet::class, 'reward_set_id', 'reward_set_id');
    }

    public function getKey()
    {
        return "{$this->source_type}:{$this->source_id}";
    }

    public function getKeyName()
    {
        return 'source_type';
    }

    protected function setKeysForSaveQuery($query)
    {
        return $query
            ->where('source_type', $this->getAttribute('source_type'))
            ->where('source_id', $this->getAttribute('source_id'));
    }
}
