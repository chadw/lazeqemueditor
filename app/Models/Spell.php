<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Spell extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'spells_new';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'int';
    protected $guarded = [];

    public function scopeHasAnyEffect($query, array $effectIds)
    {
        return $query->where(function ($q) use ($effectIds) {
            foreach (range(1, 12) as $i) {
                $q->orWhereIn("effectid{$i}", $effectIds);
            }
        });
    }

    public function desc(): BelongsTo
    {
        return $this->belongsTo(DbStr::class, 'descnum', 'id')
            ->where('type', 6);
    }

    public function typedesc(): BelongsTo
    {
        return $this->belongsTo(DbStr::class, 'typedescnum', 'id')
            ->where('type', 6);
    }

    public function effectdesc(): BelongsTo
    {
        return $this->belongsTo(DbStr::class, 'effectdescnum', 'id')
            ->where('type', 6);
    }

    public function effectdesc2(): BelongsTo
    {
        return $this->belongsTo(DbStr::class, 'effectdescnum2', 'id')
            ->where('type', 6);
    }

    public function scrolleffect(): HasMany
    {
        return $this->hasMany(Item::class, 'scrolleffect', 'id')
            ->select('id', 'Name', 'icon', 'scrolleffect');
    }

    public function clickeffect(): HasMany
    {
        return $this->hasMany(Item::class, 'clickeffect', 'id')
            ->select('id', 'Name', 'icon', 'clickeffect');
    }

    public function proceffect(): HasMany
    {
        return $this->hasMany(Item::class, 'proceffect', 'id')
            ->select('id', 'Name', 'icon', 'proceffect');
    }

    public function worneffect(): HasMany
    {
        return $this->hasMany(Item::class, 'worneffect', 'id')
            ->select('id', 'Name', 'icon', 'worneffect');
    }

    public function focuseffect(): HasMany
    {
        return $this->hasMany(Item::class, 'focuseffect', 'id')
            ->select('id', 'Name', 'icon', 'focuseffect');
    }

    public function pets(): HasOne
    {
        return $this->hasOne(Pet::class, 'type', 'teleport_zone')
            ->where('temp', 0);
    }

    public function npcs(): HasOne
    {
        return $this->hasOne(NpcType::class, 'name', 'teleport_zone')
            ->select('id', 'name', 'race', 'level', 'class', 'hp', 'mana', 'AC', 'mindmg', 'maxdmg');
    }

    public function recourseLink(): BelongsTo
    {
        return $this->belongsTo(Spell::class, 'RecourseLink', 'id')
            ->select('id', 'name');
    }

    public function comp1(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'components1', 'id')
            ->select('id', 'Name');
    }

    public function comp2(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'components2', 'id')
            ->select('id', 'Name');
    }

    public function comp3(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'components3', 'id')
            ->select('id', 'Name');
    }

    public function comp4(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'components4', 'id')
            ->select('id', 'Name');
    }

    public function reagent1(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'NoexpendReagent1', 'id')
            ->select('id', 'Name');
    }

    public function reagent2(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'NoexpendReagent2', 'id')
            ->select('id', 'Name');
    }

    public function reagent3(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'NoexpendReagent3', 'id')
            ->select('id', 'Name');
    }

    public function reagent4(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'NoexpendReagent4', 'id')
            ->select('id', 'Name');
    }
}
