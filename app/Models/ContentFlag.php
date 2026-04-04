<?php

namespace App\Models;

class ContentFlag extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'content_flags';
    public $timestamps = false;

    protected $fillable = [
        'flag_name',
        'enabled',
        'notes',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];
}
