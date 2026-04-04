<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountReward extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'account_rewards';
    public $timestamps = false;

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
}
