<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GlobalLoot extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'global_loot';
    public $timestamps = false;

    protected $fillable = [
        'description',
        'loottable_id',
        'enabled',
        'min_level',
        'max_level',
        'rare',
        'raid',
        'race',
        'class',
        'bodytype',
        'zone',
        'hot_zone',
        'min_expansion',
        'max_expansion',
        'content_flags',
        'content_flags_disabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public function loottable(): BelongsTo
    {
        return $this->belongsTo(LootTable::class, 'loottable_id', 'id');
    }
}
