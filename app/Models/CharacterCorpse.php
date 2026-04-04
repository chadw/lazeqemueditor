<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CharacterCorpse extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'character_corpses';
    public $timestamps = false;

    public function character(): BelongsTo
    {
        return $this->belongsTo(CharacterData::class, 'charid');
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class, 'zone_id', 'zoneidnumber')
            ->select('zoneidnumber', 'short_name');
    }

    public function corpseItems(): HasMany
    {
        return $this->hasMany(CharacterCorpseItem::class, 'corpse_id');
    }
}
