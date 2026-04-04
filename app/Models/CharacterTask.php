<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterTask extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'character_tasks';
    protected $primaryKey = ['charid', 'taskid'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'charid',
        'taskid',
        'slot',
        'type',
        'acceptedtime',
        'was_rewarded',
    ];

    protected $casts = [
        'was_rewarded' => 'boolean',
        'acceptedtime' => 'datetime',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'taskid');
    }

    public function character(): BelongsTo
    {
        return $this->belongsTo(CharacterData::class, 'charid', 'id')
            ->select('id', 'name', 'class');
    }
}
