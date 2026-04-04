<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SharedTaskActivityState extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'shared_task_activity_state';
    public $incrementing = false;
    protected $primaryKey = null;
    public $timestamps = false;

    protected $fillable = [
        'shared_task_id',
        'activity_id',
        'done_count',
        'updated_time',
        'completed_time',
    ];

    protected $casts = [
        'updated_time' => 'datetime',
        'completed_time' => 'datetime',
    ];

    public function sharedTask(): BelongsTo
    {
        return $this->belongsTo(SharedTask::class, 'shared_task_id');
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(TaskActivity::class, 'activity_id', 'activityid');
    }
}
