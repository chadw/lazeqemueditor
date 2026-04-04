<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AaRank extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'aa_ranks';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'upper_hotkey_sid',
        'lower_hotkey_sid',
        'title_sid',
        'desc_sid',
        'cost',
        'level_req',
        'spell',
        'spell_type',
        'recast_time',
        'expansion',
        'prev_id',
        'next_id',
    ];

    public function ability(): BelongsTo
    {
        return $this->belongsTo(AaAbility::class, 'id', 'first_rank_id');
    }

    public function character(): BelongsTo
    {
        return $this->belongsTo(CharacterData::class, 'id');
    }

    public function effects(): HasMany
    {
        return $this->hasMany(AaRankEffect::class, 'rank_id', 'id');
    }

    public function prereqs(): HasMany
    {
        return $this->hasMany(AaRankPrereq::class, 'rank_id', 'id');
    }

    public function previous(): BelongsTo
    {
        return $this->belongsTo(AaRank::class, 'prev_id');
    }

    public function nextRank(): HasOne
    {
        return $this->hasOne(AaRank::class, 'id', 'next_id');
            //->with(['effects', 'prereqs', 'spell_', 'nextRank']);
    }

    public function next(): BelongsTo
    {
        return $this->belongsTo(AaRank::class, 'next_id');
    }

    public function spell_(): HasOne
    {
        return $this->hasOne(Spell::class, 'id', 'spell')
            ->where('id', '>', 0);
    }
}
