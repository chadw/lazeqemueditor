<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerEventLog extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'player_event_logs';
    public $timestamps = false;

    protected $casts = [
        'created_at' => 'datetime',
        'event_data' => 'array',
    ];

    public function character(): BelongsTo
    {
        return $this->belongsTo(CharacterData::class, 'character_id')
            ->select('id', 'name');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id')
            ->select('id', 'name');
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class, 'zone_id', 'zoneidnumber')
            ->select('zoneidnumber', 'short_name', 'long_name');
    }
}
