<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Kyslik\ColumnSortable\Sortable;

class Merchantlist extends BaseModel
{
    use Sortable;

    protected $connection = 'eqemu';
    protected $table = 'merchantlist';
    protected $primaryKey = 'merchantid';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'merchantid',
        'slot',
        'item',
        'faction_required',
        'level_required',
        'min_status',
        'max_status',
        'alt_currency_cost',
        'classes_required',
        'probability',
        'bucket_name',
        'bucket_value',
        'bucket_comparison',
        'min_expansion',
        'max_expansion',
        'content_flags',
        'content_flags_disabled',
    ];

    public array $sortable = [
        'slot',
        'item',
        'buy',
        'sell',
        'alt_currency_cost',
    ];

    public function itemSortable($query, $direction)
    {
        return $query->leftJoin('items', 'merchantlist.item', '=', 'items.id')
            ->orderBy('items.Name', $direction)
            ->select('merchantlist.*');
    }

    public function buySortable($query, $direction)
    {
        return $query->leftJoin('items', 'merchantlist.item', '=', 'items.id')
            ->orderBy('items.price', $direction)
            ->select('merchantlist.*');
    }

    public function sellSortable($query, $direction)
    {
        return $query->leftJoin('items', 'merchantlist.item', '=', 'items.id')
            ->orderByRaw('(COALESCE(items.price,0) * COALESCE(items.sellrate,1)) ' . $direction)
            ->select('merchantlist.*');
    }

    public function npc(): BelongsTo
    {
        return $this->belongsTo(NpcType::class, 'merchantid', 'merchant_id');
    }

    public function items(): HasOne
    {
        return $this->hasOne(Item::class, 'id', 'item')
            ->select([
                'id',
                'Name',
                'icon',
                'itemtype',
                'slots',
                'bagslots',
                'bagwr',
                'augtype',
                'price',
                'sellrate',
                'pointtype',
                'ldontheme',
                'ldonsold',
                'ldonprice',
                'ldonsellbackrate',
            ]);
    }
}
