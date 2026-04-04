<?php

namespace App\Models;

class Variable extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'variables';
    public $timestamps = false;

    protected $fillable = [
        'varname',
        'value',
        'information',
        'ts',
    ];
}
