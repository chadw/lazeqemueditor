<?php

namespace App\Models;

class PlayerEventLogSetting extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'player_event_log_settings';
    public $timestamps = false;

    protected $fillable = [
        'event_name',
        'event_enabled',
        'retention_days',
        'discord_webhook_id',
        'etl_enabled',
    ];

    protected $casts = [
        'event_enabled' => 'boolean',
        'etl_enabled' => 'boolean',
    ];
}
