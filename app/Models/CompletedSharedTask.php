<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompletedSharedTask extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'completed_shared_tasks';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'task_id',
        'accepted_time',
        'expire_time',
        'completion_time',
        'is_locked',
    ];

    protected $casts = [
        'is_locked' => 'boolean',
        'accepted_time' => 'datetime',
        'expire_time' => 'datetime',
        'completion_time' => 'datetime',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function taskActivities(): HasMany
    {
        return $this->hasMany(TaskActivity::class, 'taskid', 'task_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(CompletedSharedTaskMember::class, 'shared_task_id', 'id');
    }

    public function activityStates(): HasMany
    {
        return $this->hasMany(CompletedSharedTaskActivityState::class, 'shared_task_id', 'id');
    }
}
