<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SharedTask extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'shared_tasks';
    public $timestamps = false;

    protected $fillable = [
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
        return $this->hasMany(SharedTaskMember::class, 'shared_task_id');
    }

    public function activityStates(): HasMany
    {
        return $this->hasMany(SharedTaskActivityState::class, 'shared_task_id');
    }

    public function dz(): HasMany
    {
        return $this->hasMany(SharedTaskDynamicZone::class, 'shared_task_id');
    }
}
