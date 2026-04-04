<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class LootTable extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'loottable';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'mincash',
        'maxcash',
        'avgcoin',
        'done',
        'min_expansion',
        'max_expansion',
        'content_flags',
        'content_flags_disabled',
    ];

    protected $casts = [
        'done' => 'boolean',
    ];

    public function loottableEntries(): HasMany
    {
        return $this->hasMany(LoottableEntry::class, 'loottable_id', 'id');
    }

    public function lootdrops(): HasManyThrough
    {
        return $this->hasManyThrough(
            LootDrop::class,
            LoottableEntry::class,
            'loottable_id',
            'id',
            'id',
            'lootdrop_id'
        );
    }

    public function globalLoot(): HasMany
    {
        return $this->hasMany(GlobalLoot::class, 'loottable_id', 'id');
    }

    public function npcs(): HasMany
    {
        return $this->hasMany(NpcType::class, 'loottable_id', 'id');
    }
}
