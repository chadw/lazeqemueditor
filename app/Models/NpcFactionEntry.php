<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NpcFactionEntry extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'npc_faction_entries';
    protected $primaryKey = 'npc_faction_id';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'faction_id',
        'value',
        'npc_value',
        'temp',
    ];

    public function npcFaction(): BelongsTo
    {
        return $this->belongsTo(NpcFaction::class, 'npc_faction_id', 'id');
    }

    public function faction(): BelongsTo
    {
        return $this->belongsTo(FactionList::class, 'faction_id', 'id');
    }
}
