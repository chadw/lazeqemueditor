<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Tribute extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'tributes';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'unknown',
        'name',
        'descr',
        'isguild',
    ];

    protected $casts = [
        'isguild' => 'boolean',
    ];

    public function levels(): HasMany
    {
        return $this->hasMany(TributeLevel::class, 'tribute_id', 'id');
    }
}
