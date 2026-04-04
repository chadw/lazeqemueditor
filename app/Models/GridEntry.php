<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GridEntry extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'grid_entries';
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = null;
    protected $keyType = 'int';

    protected $fillable = [
        'zoneid',
        'gridid',
        'number',
        'x',
        'y',
        'z',
        'heading',
        'pause',
        'centerpoint',
    ];

    protected $casts = [
        'centerpoint' => 'boolean',
    ];

    public function grid(): BelongsTo
    {
        return $this->belongsTo(Grid::class, 'gridid', 'id')
            ->where('zoneid', $this->zoneid);
    }

    protected function setKeysForSaveQuery($query): Builder
    {
        return $query
            ->where('zoneid', $this->getAttribute('zoneid'))
            ->where('gridid', $this->getAttribute('gridid'))
            ->where('number', $this->getAttribute('number'));
    }
}
