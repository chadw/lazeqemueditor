<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AaRankPrereq extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'aa_rank_prereqs';
    public $incrementing = false;
    protected $primaryKey = null;
    public $timestamps = false;

    protected $fillable = [
        'rank_id',
        'aa_id',
        'points',
    ];

    public function rank(): BelongsTo
    {
        return $this->belongsTo(AaRank::class, 'rank_id');
    }

    public function prerequisiteAa(): BelongsTo
    {
        return $this->belongsTo(AaRank::class, 'aa_id', 'id');
    }

    public function ability(): BelongsTo
    {
        return $this->belongsTo(AaAbility::class, 'aa_id')
            ->select('id', 'name');
    }
}
