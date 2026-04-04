<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Kyslik\ColumnSortable\Sortable;

class TraderAudit extends Model
{
    use Sortable;
    protected $connection = 'eqemu';
    protected $table = 'trader_audit';
    protected $primaryKey = null;
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $guarded = [];

    protected $casts = [
        'time' => 'datetime',
    ];

    public array $sortable = [
        'seller',
        'buyer',
        'itemname',
        'quantity',
        'time',
    ];

    public function sellerCharacter(): BelongsTo
    {
        return $this->belongsTo(CharacterData::class, 'seller', 'name')
            ->select('id', 'name');
    }

    public function buyerCharacter(): BelongsTo
    {
        return $this->belongsTo(CharacterData::class, 'buyer', 'name')
            ->select('id', 'name');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'itemname', 'Name')
            ->select('id', 'Name', 'icon');
    }
}
