<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class GuildBank extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'guild_bank';
    protected $primaryKey = 'guild_id';
    public $timestamps = false;

    public function guild(): BelongsTo
    {
        return $this->belongsTo(Guild::class, 'guild_id');
    }

    public function item(): HasOne
    {
        return $this->hasOne(Item::class, 'id', 'item_id')
            ->select('id', 'Name', 'icon');
    }
}
