<?php

namespace App\Models;

class GuildPermission extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'guild_permissions';
    protected $primaryKey = 'guild_id';
    public $timestamps = false;

    public function guild()
    {
        return $this->belongsTo(Guild::class, 'guild_id');
    }
}
