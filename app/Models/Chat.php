<?php

namespace App\Models;

class Chat extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'chatchannels';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'owner',
        'password',
        'minstatus',
    ];
}
