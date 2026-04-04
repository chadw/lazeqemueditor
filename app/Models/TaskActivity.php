<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class TaskActivity extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'task_activities';
    protected $primaryKey = 'activityid';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'taskid',
        'activityid',
        'req_activity_id',
        'step',
        'activitytype',
        'target_name',
        'goalmethod',
        'goalcount',
        'description_override',
        'npc_match_list',
        'item_id_list',
        'item_list',
        'dz_switch_id',
        'min_x',
        'min_y',
        'min_z',
        'max_x',
        'max_y',
        'max_z',
        'skill_list',
        'spell_list',
        'zones',
        'zone_version',
        'optional',
        'list_group',
    ];

    protected $casts = [
        'optional' => 'boolean',
    ];

    public function getActivityTypeAttribute(): string
    {
        return match ($this->attributes['activitytype'] ?? null) {
            1   => 'Deliver',
            2   => 'Kill',
            3   => 'Loot',
            4   => 'Speak With',
            5   => 'Explore',
            6   => 'Tradeskill',
            7   => 'Fish',
            8   => 'Forage',
            9   => 'Use',
            10  => 'Use',
            11  => 'Touch',
            100 => 'Give Cash',
            default => 'Unknown',
        };
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'taskid', 'id');
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class, 'zones', 'zoneidnumber')
            ->select('zoneidnumber', 'short_name', 'long_name');
    }

    protected function setKeysForSaveQuery($query): Builder
    {
        return $query->where('taskid', $this->getAttribute('taskid'))
            ->where('activityid', $this->getAttribute('activityid'));
    }

    protected function setKeysForSelectQuery($query)
    {
        return $query->where('taskid', $this->getAttribute('taskid'))
            ->where('activityid', $this->getAttribute('activityid'));
    }
}
