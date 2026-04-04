<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class LootDrop extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'lootdrop';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'min_expansion',
        'max_expansion',
        'content_flags',
        'content_flags_disabled',
    ];

    public function loottableEntries(): HasMany
    {
        return $this->hasMany(LoottableEntry::class, 'lootdrop_id', 'id');
    }

    public function entries(): HasMany
    {
        return $this->hasMany(LootdropEntry::class, 'lootdrop_id', 'id');
    }
}
