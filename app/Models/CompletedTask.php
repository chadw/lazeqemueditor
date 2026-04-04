<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompletedTask extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'completed_tasks';
    public $incrementing = false;
    protected $primaryKey = null;
    public $timestamps = false;

    protected $fillable = [
        'charid',
        'completedtime',
        'taskid',
        'activityid',
    ];

    protected $casts = [
        'completedtime' => 'datetime',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'taskid', 'id');
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(TaskActivity::class, 'activityid', 'activityid')
            ->whereColumn('taskid', 'completed_tasks.taskid');
    }

    public function character(): BelongsTo
    {
        return $this->belongsTo(CharacterData::class, 'charid', 'id')
            ->select('id', 'name', 'class');
    }
}
