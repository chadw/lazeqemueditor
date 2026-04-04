<?php

namespace App\Models;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AaAbility extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'aa_ability';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
        'category',
        'classes',
        'races',
        'drakkin_heritage',
        'deities',
        'status',
        'type',
        'charges',
        'grant_only',
        'first_rank_id',
        'enabled',
        'reset_on_death',
        'auto_grant_enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'grant_only' => 'boolean',
        'reset_on_death' => 'boolean',
        'auto_grant_enabled' => 'boolean',
    ];

    public function scopeClassBit($query, $classBit)
    {
        return $query->whereRaw('classes & ? != 0', [$classBit]);
    }

    public function rankChain(): Collection
    {
        $ranks = collect();

        $rank = AaRank::with([
            'effects',
            'prereqs.ability',
            'spell_:id,name,new_icon',
        ])
            ->find($this->first_rank_id);

        while ($rank) {
            $ranks->push($rank);

            $rank = $rank->next_id > 0
                ? AaRank::with([
                    'effects',
                    'prereqs.ability',
                    'spell_:id,name,new_icon',
                ])->find($rank->next_id)
                : null;
        }

        return $ranks;
    }

    public function firstRank(): BelongsTo
    {
        return $this->belongsTo(AaRank::class, 'first_rank_id');
    }

    public function ranks(): HasMany
    {
        return $this->hasMany(AaRank::class, 'id', 'first_rank_id')
            ->orWhereIn('id', function ($q) {
                $q->select('id')
                    ->from('aa_ranks')
                    ->where('prev_id', '>=', 0);
            });
    }
}
