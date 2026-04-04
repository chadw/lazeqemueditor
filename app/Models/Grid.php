<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grid extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'grid';

    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = null;
    protected $keyType = 'int';

    protected $fillable = [
        'id',
        'zoneid',
        'type',
        'type2',
    ];

    public function entries(): HasMany
    {
        return $this->hasMany(GridEntry::class, 'gridid', 'id')
            ->orderBy('number');
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class, 'zoneid', 'zoneidnumber')
            ->select('zoneidnumber', 'short_name', 'long_name');
    }

    protected function setKeysForSaveQuery($query): Builder
    {
        return $query
            ->where('zoneid', $this->getAttribute('zoneid'))
            ->where('id', $this->getAttribute('id'));
    }
}
