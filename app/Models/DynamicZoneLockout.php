<?php

namespace App\Models;

use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Kyslik\ColumnSortable\Sortable;

class DynamicZoneLockout extends BaseModel
{
    use Sortable;
    protected $connection = 'eqemu';
    protected $table = 'dynamic_zone_lockouts';
    public $timestamps = false;

    protected $fillable = [
        'dynamic_zone_id',
        'event_name',
        'expire_time',
        'duration',
        'from_expedition_uuid',
    ];

    protected $casts = [
        'expire_time' => 'datetime',
    ];

    public array $sortable = [
        'dynamic_zone_name',
        'event_name',
        'expire_time',
    ];

    protected function cleanDuration(): Attribute
    {
        return Attribute::get(function () {
            return CarbonInterval::seconds($this->duration)->cascade();
        });
    }

    public function dz(): BelongsTo
    {
        return $this->belongsTo(DynamicZone::class, 'dynamic_zone_id')
            ->select('id', 'name');
    }

    public function dynamicZoneNameSortable($query, $direction)
    {
        return $query
            ->join('dynamic_zones', 'dynamic_zones.id', '=', 'dynamic_zone_lockouts.dynamic_zone_id')
            ->orderBy('dynamic_zones.name', $direction)
            ->select('dynamic_zone_lockouts.*');
    }
}
