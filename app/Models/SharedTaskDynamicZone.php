<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SharedTaskDynamicZone extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'shared_task_dynamic_zones';
    public $incrementing = false;
    protected $primaryKey = null;
    public $timestamps = false;

    protected $fillable = [
        'shared_task_id',
        'dynamic_zone_id',
    ];

    public function sharedTask(): BelongsTo
    {
        return $this->belongsTo(SharedTask::class, 'shared_task_id');
    }

    public function dz(): BelongsTo
    {
        return $this->belongsTo(DynamicZone::class, 'dynamic_zone_id');
    }
}
