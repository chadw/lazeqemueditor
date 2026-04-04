<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class FactionAssociation extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'faction_association';
    public $timestamps = false;

    protected $fillable = [
        'id_1',
        'mod_1',
        'id_2',
        'mod_2',
        'id_3',
        'mod_3',
        'id_4',
        'mod_4',
        'id_5',
        'mod_5',
        'id_6',
        'mod_6',
        'id_7',
        'mod_7',
        'id_8',
        'mod_8',
        'id_9',
        'mod_9',
        'id_10',
        'mod_10',
    ];

    public function getFactionsCountAttribute(): int
    {
        return collect(range(1, 10))
            ->filter(fn($i) => !empty($this->{"id_$i"}) && $this->{"faction$i"} !== null)
            ->count();
    }

    public function factionList(): HasOne
    {
        return $this->hasOne(FactionList::class, 'id', 'id')
            ->orderBy('name', 'asc');
    }

    public function faction1(): BelongsTo
    {
        return $this->belongsTo(FactionList::class, 'id_1');
    }

    public function faction2(): BelongsTo
    {
        return $this->belongsTo(FactionList::class, 'id_2');
    }

    public function faction3(): BelongsTo
    {
        return $this->belongsTo(FactionList::class, 'id_3');
    }

    public function faction4(): BelongsTo
    {
        return $this->belongsTo(FactionList::class, 'id_4');
    }

    public function faction5(): BelongsTo
    {
        return $this->belongsTo(FactionList::class, 'id_5');
    }

    public function faction6(): BelongsTo
    {
        return $this->belongsTo(FactionList::class, 'id_6');
    }

    public function faction7(): BelongsTo
    {
        return $this->belongsTo(FactionList::class, 'id_7');
    }

    public function faction8(): BelongsTo
    {
        return $this->belongsTo(FactionList::class, 'id_8');
    }

    public function faction9(): BelongsTo
    {
        return $this->belongsTo(FactionList::class, 'id_9');
    }

    public function faction10(): BelongsTo
    {
        return $this->belongsTo(FactionList::class, 'id_10');
    }
}
