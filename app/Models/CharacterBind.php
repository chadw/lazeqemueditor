<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterBind extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'character_bind';
    public $timestamps = false;

    public function character(): BelongsTo
    {
        return $this->belongsTo(CharacterData::class, 'id');
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class, 'zone_id', 'zoneidnumber')
            ->select('zoneidnumber', 'short_name');
    }
}
