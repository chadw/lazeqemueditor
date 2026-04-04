<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use App\Models\SpawnTwoDisabled;

class SpawnTwo extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'spawn2';
    public $timestamps = false;

    protected $fillable = [
        'spawngroupID',
        'zone',
        'version',
        'x',
        'y',
        'z',
        'heading',
        'respawntime',
        'variance',
        'pathgrid',
        'path_when_zone_idle',
        '_condition',
        'cond_value',
        'animation',
        'min_expansion',
        'max_expansion',
        'content_flags',
        'content_flags_disabled',
    ];

    protected $casts = [
        'path_when_zone_idle' => 'boolean',
    ];

    public function zoneData(): BelongsTo
    {
        return $this->belongsTo(Zone::class, 'zone', 'short_name');
    }

    public function spawnGroup(): BelongsTo
    {
        return $this->belongsTo(SpawnGroup::class, 'spawngroupID', 'id');
    }

    public function spawnentries(): HasMany
    {
        return $this->hasMany(SpawnEntry::class, 'spawngroupID', 'spawngroupID');
    }

    public function npcs(): HasManyThrough
    {
        return $this->hasManyThrough(
            NpcType::class,
            SpawnEntry::class,
            'spawngroupID',
            'id',
            'spawngroupID',
            'npcID'
        )->select([
            'npc_types.id',
            'npc_types.name',
        ]);
    }

    public function spawn2Disabled(): HasMany
    {
        return $this->hasMany(SpawnTwoDisabled::class, 'spawn2_id', 'id');
    }
}
