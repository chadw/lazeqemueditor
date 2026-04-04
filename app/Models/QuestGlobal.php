<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestGlobal extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'quest_globals';
    public $timestamps = false;

    protected $fillable = [
        'charid',
        'npcid',
        'zoneid',
        'name',
        'value',
        'expdate',
    ];

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class, 'zoneid', 'zoneidnumber');
    }

    public function character(): BelongsTo
    {
        return $this->belongsTo(CharacterData::class, 'charid');
    }

    public function npc(): BelongsTo
    {
        return $this->belongsTo(NpcType::class, 'npcid');
    }
}
