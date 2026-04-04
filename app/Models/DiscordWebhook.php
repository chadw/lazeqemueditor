<?php

namespace App\Models;

class DiscordWebhook extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'discord_webhooks';
    public $timestamps = true;

    const UPDATED_AT = null;

    protected $fillable = [
        'webhook_name',
        'webhook_url',
        'created_at',
        'deleted_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}
