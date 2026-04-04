<?php

namespace App\Models;

class ChatReservedName extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'chatchannel_reserved_name';
    public $timestamps = false;

    protected $fillable = [
        'name',
    ];
}
