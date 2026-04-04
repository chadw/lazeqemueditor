<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SkillCap extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'skill_caps';
    public $timestamps = false;

    protected $fillable = [
        'class_id',
        'skill_id',
        'level',
        'cap'
    ];
}
