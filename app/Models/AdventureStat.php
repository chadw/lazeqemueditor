<?php

namespace App\Models;

class AdventureStat extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'adventure_stats';
    public $timestamps = false;
}
