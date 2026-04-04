<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class ItemEvolvingDetail extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'items_evolving_details';
    public $timestamps = false;

    protected $fillable = [
        'item_evo_id',
        'item_evolve_level',
        'item_id',
        'type',
        'sub_type',
        'required_amount',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id', 'id')
            ->select('id', 'Name', 'icon', 'evoitem', 'evoid', 'evolvinglevel', 'evomax');
    }

    public static function evolvingOptions(): Collection
    {
        return self::with('item')
            ->select('item_evo_id', 'item_evolve_level', 'item_id')
            ->orderBy('item_evo_id')
            ->get();
    }
}
