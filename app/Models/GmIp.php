<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GmIp extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'gm_ips';
    public $timestamps = false;

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
}
