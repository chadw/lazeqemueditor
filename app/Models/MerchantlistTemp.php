<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MerchantlistTemp extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'merchantlist_temp';
    protected $primaryKey = 'npcid';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'npcid',
        'slot',
        'zone_id',
        'instance_id',
        'itemid',
        'charges',
    ];

    public function npc(): BelongsTo
    {
        return $this->belongsTo(NpcType::class, 'npcid', 'id');
    }

    public function items(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'itemid', 'id')
            ->select([
                'id', 'Name', 'icon', 'itemtype', 'slots', 'bagslots', 'bagwr', 'augtype', 'price',
                'pointtype', 'ldontheme', 'ldonsold', 'ldonprice',
            ]);
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class, 'zone_id', 'zoneidnumber');
    }
}
