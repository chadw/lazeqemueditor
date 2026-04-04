<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ZonePoint extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'zone_points';
    public $timestamps = false;

    protected $fillable = [
        'zone',
        'version',
        'number',
        'y',
        'x',
        'z',
        'heading',
        'target_y',
        'target_x',
        'target_z',
        'target_heading',
        'zoneinst',
        'target_zone_id',
        'target_instance',
        'buffer',
        'client_version_mask',
        'min_expansion',
        'max_expansion',
        'content_flags',
        'content_flags_disabled',
        'is_virtual',
        'height',
        'width',
    ];

    protected $casts = [
        'is_virtual' => 'boolean',
    ];

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class, 'zone', 'short_name');
    }

    public function targetZones(): BelongsTo
    {
        return $this->belongsTo(Zone::class, 'target_zone_id', 'zoneidnumber')
            ->select('zoneidnumber', 'short_name', 'long_name', 'version');
    }
}
