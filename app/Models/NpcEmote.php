<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NpcEmote extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'npc_emotes';
    public $timestamps = false;

    protected $fillable = [
        'emoteid',
        'event_',
        'type',
        'text',
    ];
}
