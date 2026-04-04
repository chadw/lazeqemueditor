<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;

class TributeLevel extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'tribute_levels';
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'tribute_id',
        'level',
        'cost',
        'item_id',
    ];

    public function tribute(): BelongsTo
    {
        return $this->belongsTo(Tribute::class, 'tribute_id', 'id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id', 'id')
            ->select('id', 'Name', 'icon');
    }
}
