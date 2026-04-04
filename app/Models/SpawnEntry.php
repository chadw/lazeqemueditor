<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\DiscordAlerts\Facades\DiscordAlert;
use App\Models\SpawnGroup;
use App\Models\NpcType;

class SpawnEntry extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'spawnentry';
    protected $primaryKey = null;
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'spawngroupID',
        'npcID',
        'chance',
        'condition_value_filter',
        'min_time',
        'max_time',
        'min_expansion',
        'max_expansion',
        'content_flags',
        'content_flags_disabled',
    ];

    public function npc(): BelongsTo
    {
        return $this->belongsTo(NpcType::class, 'npcID', 'id')
            ->select(['id', 'name', 'level']);
    }

    public function spawnGroup(): BelongsTo
    {
        return $this->belongsTo(SpawnGroup::class, 'spawngroupID', 'id');
    }

    public function spawn2(): HasOne
    {
        return $this->hasOne(SpawnTwo::class, 'spawngroupID', 'spawngroupID');
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        $activity->subject_id = "{$this->spawngroupID}-{$this->npcID}";
        parent::tapActivity($activity, $eventName);
    }

    public function getKey()
    {
        return "{$this->spawngroupID}-{$this->npcID}";
    }

    protected function setKeysForSaveQuery($query): Builder
    {
        return $query
            ->where('spawngroupID', $this->getAttribute('spawngroupID'))
            ->where('npcID', $this->getAttribute('npcID'));
    }

    protected function setKeysForSelectQuery($query): Builder
    {
        return $query->where('spawngroupID', $this->getAttribute('spawngroupID'))
            ->where('npcID', $this->getAttribute('npcID'));
    }
}
