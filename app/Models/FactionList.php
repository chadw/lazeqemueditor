<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

class FactionList extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'faction_list';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'base',
    ];

    public function basedata(): HasOne
    {
        return $this->hasOne(FactionBaseData::class, 'client_faction_id', 'id');
    }

    public function mod(): HasMany
    {
        return $this->hasMany(FactionListMod::class, 'faction_id', 'id');
    }

    public static function selectFactions(): array
    {
        return self::select('id', 'name')
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn($f) => [
                $f->id => $f->name
            ])
            ->toArray();
    }

    public static function options(): Collection
    {
        return self::select('id', 'name')
            ->orderBy('name')
            ->groupBy('id')
            ->get();
    }
}
