<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Kyslik\ColumnSortable\Sortable;

class DynamicZone extends BaseModel
{
    use Sortable;

    protected $connection = 'eqemu';
    protected $table = 'dynamic_zones';
    public $timestamps = false;

    protected $fillable = [
        'instance_id',
        'type',
        'name',
        'leader_id',
        'min_players',
        'max_players',
        'dz_switch_id',
        'compass_zone_id',
        'compass_x',
        'compass_y',
        'compass_z',
        'safe_return_zone_id',
        'safe_return_x',
        'safe_return_y',
        'safe_return_z',
        'safe_return_heading',
        'zone_in_x',
        'zone_in_y',
        'zone_in_z',
        'zone_in_heading',
        'has_zone_in',
        'is_locked',
        'add_replay',
    ];

    protected $casts = [
        'add_replay'  => 'boolean',
        'is_locked'   => 'boolean',
        'has_zone_in' => 'boolean',
    ];

    public array $sortable = [
        'name',
        'leader_name',
    ];

    public function leader(): BelongsTo
    {
        return $this->belongsTo(CharacterData::class, 'leader_id');
    }

    public function safe_zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class, 'safe_return_zone_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(DynamicZoneMember::class, 'dynamic_zone_id');
    }

    public function lockouts(): HasMany
    {
        return $this->hasMany(DynamicZoneLockout::class, 'dynamic_zone_id');
    }

    public function leaderNameSortable($query, $direction)
    {
        return $query
            ->join('character_data', 'character_data.id', '=', 'dynamic_zones.leader_id')
            ->orderBy('character_data.name', $direction);
    }
}
