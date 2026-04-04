<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class SpawnGroup extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'spawngroup';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'spawn_limit',
        'dist',
        'max_x',
        'min_x',
        'max_y',
        'min_y',
        'delay',
        'mindelay',
        'despawn',
        'despawn_timer',
        'wp_spawns',
    ];

    protected $casts = [
        'wp_spawns' => 'boolean',
    ];

    public function spawn2(): HasMany
    {
        return $this->hasMany(SpawnTwo::class, 'spawngroupID', 'id');
    }

    public function spawnentries(): HasMany
    {
        return $this->hasMany(SpawnEntry::class, 'spawngroupID', 'id');
    }

    public function npcs(): HasManyThrough
    {
        return $this->hasManyThrough(
            NpcType::class,
            SpawnEntry::class,
            'spawngroupID',
            'id',
            'id',
            'npcID'
        );
    }
}
