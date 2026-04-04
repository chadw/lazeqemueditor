<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DynamicZoneMember extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'dynamic_zone_members';
    public $timestamps = false;

    protected $fillable = [
        'dynamic_zone_id',
        'character_id',
    ];

    public function character(): BelongsTo
    {
        return $this->belongsTo(CharacterData::class, 'character_id');
    }
}
