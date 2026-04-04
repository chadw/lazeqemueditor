<?php

namespace App\Models;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Kyslik\ColumnSortable\Sortable;

class AlternateCurrency extends BaseModel
{
    use Sortable;

    protected $connection = 'eqemu';
    protected $table = 'alternate_currency';
    protected $primaryKey = 'id';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'id',
        'item_id',
    ];

    public array $sortable = [
        'id',
        'item_id',
        'item'
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id', 'id')
            ->select('id', 'Name', 'icon');
    }

    public function itemSortable($query, $direction)
    {
        return $query
            ->leftJoin('items', 'alternate_currency.item_id', '=', 'items.id')
            ->orderBy('items.Name', $direction)
            ->select('alternate_currency.*');
    }

    public static function allAltCurrency(): Collection
    {
        return Cache::remember('alt_currency', now()->addMonth(), function () {
            return self::with('item:id,Name,icon')->get();
        });
    }
}
