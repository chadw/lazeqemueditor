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

    public function getKey()
    {
        return "{$this->rank_id}-{$this->aa_id}";
    }

    public function getKeyName()
    {
        return 'rank_id';
    }

    protected function setKeysForSaveQuery($query)
    {
        return $query->where('rank_id', $this->getAttribute('rank_id'))
            ->where('aa_id', $this->getAttribute('aa_id'));
    }

    protected function setKeysForSelectQuery($query)
    {
        return $query->where('rank_id', $this->getAttribute('rank_id'))
            ->where('aa_id', $this->getAttribute('aa_id'));
    }
}
