<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;

class KeyRing extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'keyring';
    public $timestamps = false;

    protected $fillable = [
        'char_id',
        'item_id',
    ];

    public function item(): HasOne
    {
        return $this->hasOne(Item::class, 'id', 'item_id')
            ->select('id', 'Name', 'icon');
    }
}
