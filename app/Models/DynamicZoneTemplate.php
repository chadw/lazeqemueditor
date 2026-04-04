<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DynamicZoneTemplate extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'dynamic_zone_templates';
    public $timestamps = false;

    protected $fillable = [
        'zone_id',
        'zone_version',
        'name',
        'min_players',
        'max_players',
        'duration_seconds',
        'dz_switch_id',
        'compass_zone_id',
        'compass_x',
        'compass_y',
        'compass_z',
        'return_zone_id',
        'return_x',
        'return_y',
        'return_z',
        'return_h',
        'override_zone_in',
        'zone_in_x',
        'zone_in_y',
        'zone_in_z',
        'zone_in_h',
    ];

    protected $casts = [
        'override_zone_in' => 'boolean',
    ];

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class, 'zone_id', 'zoneidnumber')
            ->select('zoneidnumber', 'short_name', 'long_name', 'safe_x', 'safe_y', 'safe_z', 'safe_heading');
    }

    public function compassZone(): BelongsTo
    {
        return $this->belongsTo(Zone::class, 'compass_zone_id', 'zoneidnumber')
            ->select('zoneidnumber', 'short_name', 'long_name', 'safe_x', 'safe_y', 'safe_z', 'safe_heading');
    }

    public function returnZone(): BelongsTo
    {
        return $this->belongsTo(Zone::class, 'return_zone_id', 'zoneidnumber')
            ->select('zoneidnumber', 'short_name', 'long_name', 'safe_x', 'safe_y', 'safe_z', 'safe_heading');
    }
}
