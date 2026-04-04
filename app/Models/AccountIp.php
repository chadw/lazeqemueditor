<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountIp extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'account_ip';
    public $timestamps = false;

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'accid');
    }
}
