<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LdonTrapTemplate extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'ldon_trap_templates';
    public $timestamps = false;

    protected $fillable = [
        'type',
        'spell_id',
        'skill',
        'locked',
    ];

    protected $casts = [
        'locked' => 'boolean',
    ];

    public function spell(): BelongsTo
    {
        return $this->belongsTo(Spell::class, 'spell_id', 'id')
            ->select('id', 'name', 'new_icon');
    }

    public function entries(): HasMany
    {
        return $this->hasMany(LdonTrapEntry::class, 'trap_id');
    }
}
