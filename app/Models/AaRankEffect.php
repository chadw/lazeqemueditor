<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AaRankEffect extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'aa_rank_effects';
    public $incrementing = false;
    protected $primaryKey = null;
    public $timestamps = false;

    protected $fillable = [
        'rank_id',
        'slot',
        'effect_id',
        'base1',
        'base2',
    ];

    public function rank(): BelongsTo
    {
        return $this->belongsTo(AaRank::class, 'rank_id');
    }
}
