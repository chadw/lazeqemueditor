<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpawnTwoDisabled extends Model
{
    protected $connection = 'eqemu';
    protected $table = 'spawn2_disabled';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'spawn2_id',
        'instance_id',
        'disabled',
    ];

    protected $casts = [
        'id' => 'integer',
        'spawn2_id' => 'integer',
        'instance_id' => 'integer',
        'disabled' => 'integer',
    ];

    public function spawn2(): BelongsTo
    {
        return $this->belongsTo(SpawnTwo::class, 'spawn2_id', 'id');
    }
}
