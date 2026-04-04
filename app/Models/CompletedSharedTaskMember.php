<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompletedSharedTaskMember extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'completed_shared_task_members';
    public $incrementing = false;
    protected $primaryKey = null;
    public $timestamps = false;

    protected $fillable = [
        'shared_task_id',
        'character_id',
        'is_leader',
    ];

    protected $casts = [
        'is_leader' => 'boolean',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(CompletedSharedTask::class, 'shared_task_id', 'id');
    }

    public function character(): BelongsTo
    {
        return $this->belongsTo(CharacterData::class, 'character_id')
            ->select('id', 'name', 'class');
    }
}
