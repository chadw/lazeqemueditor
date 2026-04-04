<?php

namespace App\Models;

class GuildRank extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'guild_ranks';
    protected $primaryKey = 'guild_id';
    public $timestamps = false;

    protected $fillable = [
        'guild_id',
        'rank',
        'title',
    ];

    public function guild()
    {
        return $this->belongsTo(Guild::class, 'guild_id');
    }
}
