<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Graveyard extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'graveyard';
    public $timestamps = false;

    protected $fillable = [
        'zone_id',
        'x',
        'y',
        'z',
        'heading',
    ];

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class, 'zone_id', 'zoneidnumber')
            ->select('zoneidnumber', 'short_name', 'long_name');
    }
}
