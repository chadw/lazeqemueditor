<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlockedSpell extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'blocked_spells';
    public $timestamps = false;

    protected $fillable = [
        'spellid',
        'type',
        'zoneid',
        'x',
        'y',
        'z',
        'x_diff',
        'y_diff',
        'z_diff',
        'message',
        'description',
        'min_expansion',
        'max_expansion',
        'content_flags',
        'content_flags_disabled',
    ];

    public function spell(): BelongsTo
    {
        return $this->belongsTo(Spell::class, 'spellid')
            ->select('id', 'name', 'new_icon');
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class, 'zoneid', 'zoneidnumber')
            ->select('zoneidnumber', 'short_name', 'long_name');
    }
}
