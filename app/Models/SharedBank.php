<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SharedBank extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'sharedbank';
    public $timestamps = false;

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id')
            ->select('id', 'Name', 'icon');
    }

    public function aug1()
    {
        return $this->belongsTo(Item::class, 'augment_one', 'id')
            ->select('id', 'Name', 'icon');
    }

    public function aug2()
    {
        return $this->belongsTo(Item::class, 'augment_two', 'id')
            ->select('id', 'Name', 'icon');
    }

    public function aug3()
    {
        return $this->belongsTo(Item::class, 'augment_three', 'id')
            ->select('id', 'Name', 'icon');
    }

    public function aug4()
    {
        return $this->belongsTo(Item::class, 'augment_four', 'id')
            ->select('id', 'Name', 'icon');
    }

    public function aug5()
    {
        return $this->belongsTo(Item::class, 'augment_five', 'id')
            ->select('id', 'Name', 'icon');
    }

    public function aug6()
    {
        return $this->belongsTo(Item::class, 'augment_six', 'id')
            ->select('id', 'Name', 'icon');
    }
}
