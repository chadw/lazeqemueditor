<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroundSpawn extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'ground_spawns';
    public $timestamps = false;

    protected $fillable = [
        'zoneid',
        'version',
        'max_x',
        'max_y',
        'max_z',
        'min_x',
        'min_y',
        'heading',
        'name',
        'item',
        'max_allowed',
        'comment',
        'respawn_timer',
        'fix_z',
        'min_expansion',
        'max_expansion',
        'content_flags',
        'content_flags_disabled',
    ];

    protected $casts = [
        'fix_z'    => 'boolean',
    ];

    public function item_(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item', 'id')
            ->select('id', 'Name', 'icon');
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class, 'zoneid', 'zoneidnumber')
            ->select('zoneidnumber', 'short_name', 'long_name');
    }
}
