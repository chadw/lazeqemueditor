<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LdonTrapEntry extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'ldon_trap_entries';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'trap_id',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(LdonTrapTemplate::class, 'trap_id');
    }
}
