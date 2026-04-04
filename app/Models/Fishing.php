<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fishing extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'fishing';
    public $timestamps = false;

    protected $fillable = [
        'zoneid',
        'Itemid',
        'skill_level',
        'chance',
        'npc_id',
        'npc_chance',
        'min_expansion',
        'max_expansion',
        'content_flags',
        'content_flags_disabled',
    ];

    public function scopeForZone($query, int $zoneId): Builder
    {
        return $query->where('zoneid', $zoneId)
            ->orWhere('zoneid', 0);
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class, 'zoneid', 'zoneidnumber')
            ->select('zoneidnumber', 'short_name', 'long_name');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'Itemid')
            ->select('id', 'Name', 'icon');
    }
}
