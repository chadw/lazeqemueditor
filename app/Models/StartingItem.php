<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StartingItem extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'starting_items';
    public $timestamps = false;

    protected $fillable = [
        'class_list',
        'race_list',
        'deity_list',
        'zone_id_list',
        'item_id',
        'item_charges',
        'augment_one',
        'augment_two',
        'augment_three',
        'augment_four',
        'augment_five',
        'augment_six',
        'status',
        'inventory_slot',
        'min_expansion',
        'max_expansion',
        'content_flags',
        'content_flags_disabled',
    ];

    public static function defaults(): array
    {
        return [
            'item_id'        => null,
            'item_charges'   => 1,
            'inventory_slot' => -1,
            'status'         => 0,
            'class_list'     => 0,
            'race_list'      => 0,
            'deity_list'     => 0,
            'zone_id_list'   => 0,
            'augment_one'    => 0,
            'augment_two'    => 0,
            'augment_three'  => 0,
            'augment_four'   => 0,
            'augment_five'   => 0,
            'augment_six'    => 0,
            'min_expansion'  => -1,
            'max_expansion'  => -1,
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id')
            ->select('id', 'Name', 'icon');
    }
}
