<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterCorpseItem extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'character_corpse_items';
    public $timestamps = false;

    public function corpse(): BelongsTo
    {
        return $this->belongsTo(CharacterCorpse::class, 'corpse_id');
    }
}
