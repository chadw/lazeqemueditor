<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompletedSharedTaskActivityState extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'completed_shared_task_activity_state';
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
        'updated_time' => 'boolean',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(CompletedSharedTask::class, 'shared_task_id', 'id');
    }

    public function taskActivity(): BelongsTo
    {
        return $this->belongsTo(TaskActivity::class, 'activity_id', 'activityid');
    }
}
