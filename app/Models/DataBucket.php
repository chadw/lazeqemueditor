<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataBucket extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'data_buckets';
    public $timestamps = false;

    protected $fillable = [
        'key',
        'value',
        'expires',
        'account_id',
        'character_id',
        'npc_id',
        'bot_id',
        'zone_id',
        'instance_id'
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id')
            ->select('id', 'name');
    }

    public function character(): BelongsTo
    {
        return $this->belongsTo(CharacterData::class, 'character_id')
            ->select('id', 'name');
    }

    public function npc(): BelongsTo
    {
        return $this->belongsTo(NpcType::class, 'npc_id')
            ->select('id', 'name');
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class, 'zone_id')
            ->select('zoneidnumber', 'short_name');
    }
}
