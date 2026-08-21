<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RewardSourceEntry extends BaseModel
{
    protected $connection = 'eqemu';

    protected $table = 'reward_source_entries';

    protected $primaryKey = null;

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'source_type',
        'source_id',
        'sequence',
        'reward_id',
    ];

    public function reward(): BelongsTo
    {
        return $this->belongsTo(AchievementReward::class, 'reward_id', 'reward_id');
    }

    public function getKey()
    {
        return "{$this->source_type}:{$this->source_id}:{$this->reward_id}";
    }

    public function getKeyName()
    {
        return 'source_type';
    }

    protected function setKeysForSaveQuery($query)
    {
        return $query
            ->where('source_type', $this->getAttribute('source_type'))
            ->where('source_id', $this->getAttribute('source_id'))
            ->where('reward_id', $this->getAttribute('reward_id'));
    }
}
