<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Item extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'items';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'int';
    protected $guarded = [];

    protected $casts = [
        'magic' => 'boolean',
        'nodrop' => 'boolean',
        'fvnodrop' => 'boolean',
        'questitemflag' => 'boolean',
        'norent' => 'boolean',
        'tradeskills' => 'boolean',
        'stackable' => 'boolean',
        'book' => 'boolean',
        'notransfer' => 'boolean',
        'summonedflag' => 'boolean',
        'artifactflag' => 'boolean',
        'nopet' => 'boolean',
        'attuneable' => 'boolean',
        'potionbelt' => 'boolean',
        'placeable' => 'boolean',
        'epicitem' => 'boolean',
        'expendablearrow' => 'boolean',
        'heirloom' => 'boolean',
        'ldonsold' => 'boolean',
        'evoitem' => 'boolean',
    ];

    public function augDistillerItem(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'augdistiller', 'id')->select('id', 'Name', 'icon');
    }

    public function procEffectSpell(): BelongsTo
    {
        return $this->belongsTo(Spell::class, 'proceffect', 'id')->select('id', 'name', 'new_icon');
    }

    public function wornEffectSpell(): BelongsTo
    {
        return $this->belongsTo(Spell::class, 'worneffect', 'id')->select('id', 'name', 'new_icon');
    }

    public function focusEffectSpell(): BelongsTo
    {
        return $this->belongsTo(Spell::class, 'focuseffect', 'id')->select('id', 'name', 'new_icon');
    }

    public function clickEffectSpell(): BelongsTo
    {
        return $this->belongsTo(Spell::class, 'clickeffect', 'id')->select('id', 'name', 'new_icon');
    }

    public function scrollEffectSpell(): BelongsTo
    {
        return $this->belongsTo(Spell::class, 'scrolleffect', 'id')->select('id', 'name', 'new_icon');
    }

    public function bardEffectSpell(): BelongsTo
    {
        return $this->belongsTo(Spell::class, 'bardeffect', 'id')->select('id', 'name', 'new_icon');
    }

    public function merchants(): HasMany
    {
        return $this->hasMany(Merchantlist::class, 'item', 'id');
    }

    public function drops(): HasMany
    {
        return $this->hasMany(LootdropEntry::class, 'item_id', 'id');
    }

    public function foraged(): HasMany
    {
        return $this->hasMany(Forage::class, 'itemid', 'id');
    }

    public function fished(): HasMany
    {
        return $this->hasMany(Fishing::class, 'itemid', 'id');
    }

    public function lootdropEntries(): HasMany
    {
        return $this->hasMany(LootdropEntry::class, 'item_id', 'id');
    }

    public function evolvingDetails(): HasMany
    {
        return $this->hasMany(ItemEvolvingDetail::class, 'item_evo_id', 'evoid')->orderBy('item_evolve_level');
    }

    /**
     * Get the formatted buy cost (0.95 multiplier).
     *
     * @return Attribute
     */
    protected function buyCost(): Attribute
    {
        return Attribute::get(fn () => $this->price * RuleValue::getMerchantMods()->buy);
    }

    /**
     * Get the formatted sell price (price * sellrate * 1.05).
     *
     * @return Attribute
     */
    protected function sellPrice(): Attribute
    {
        return Attribute::get(function () {
            $mod = RuleValue::getMerchantMods()->sell;

            return $this->price * ($this->sellrate ?? 1.0) * $mod;
        });
    }

    /**
     * Get the main item tags
     *
     * @return Attribute
     */
    protected function tagString(): Attribute
    {
        return Attribute::get(fn () => collect([
            'Augment'           => $this->itemtype == 54,
            'Magic'             => $this->magic == 1,
            'Lore'              => $this->loregroup == -1,
            'No Trade'          => $this->nodrop == 0,
            'FV No Trade'       => $this->fvnodrop == 1,
            'No Rent'           => $this->norent == 0,
            'Quest'             => $this->questitemflag == 1,
            'Attuneable'        => $this->attuneable == 1,
            'Tradeskill Item'   => $this->tradeskills == 1,
            'Book'              => $this->book == 1,
            'No Transfer'       => $this->notransfer == 1,
            'Summoned'          => $this->summonedflag == 1,
            'Artifact'          => $this->artifactflag == 1,
            'No Pet'            => $this->nopet == 1,
            'Stackable'         => $this->stackable == 1,
            'Potion Belt'       => $this->potionbelt == 1,
            'Placeable'         => $this->placeable == 1,
            'Epic'              => $this->epicitem == 1,
            'Arrow Expendable'  => $this->expendablearrow == 1,
            'Heirloom'          => $this->heirloom == 1,
        ])->filter()->keys()->implode(', '));
    }
}
