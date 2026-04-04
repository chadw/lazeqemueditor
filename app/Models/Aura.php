<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Contracts\Activity;

class Aura extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'auras';
    protected $primaryKey = 'type';
    public $timestamps = false;

    protected $fillable = [
        'type',
        'npc_type',
        'name',
        'spell_id',
        'distance',
        'aura_type',
        'spawn_type',
        'movement',
        'duration',
        'icon',
        'cast_time'
    ];

    public function getRouteKeyName(): string
    {
        return 'type';
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        $activity->subject_id = "{$this->type}";
        parent::tapActivity($activity, $eventName);
    }

    public function getKey()
    {
        return "{$this->type}";
    }

    public function getKeyName()
    {
        return 'type';
    }

    public function spell(): BelongsTo
    {
        return $this->belongsTo(Spell::class, 'spell_id', 'id')
            ->select('id', 'name', 'new_icon', 'targettype');
    }

    public function npc(): BelongsTo
    {
        return $this->belongsTo(NpcType::class, 'npc_type', 'id')
            ->select('id', 'name');
    }
}
