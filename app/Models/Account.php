<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Kyslik\ColumnSortable\Sortable;

class Account extends BaseModel
{
    use Sortable;

    protected $connection = 'eqemu';
    protected $table = 'account';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'charname',
        'auto_login_charname',
        'sharedplat',
        'password',
        'status',
        'ls_id',
        'lsaccount_id',
        'gmspeed',
        'invulnerable',
        'flymode',
        'ignore_tells',
        'revoked',
        'karma',
        'minilogin_ip',
        'hideme',
        'rulesflag',
        'suspendeduntil',
        'time_creation',
        'ban_reason',
        'suspend_reason',
        'crc_eqgame',
        'crc_skillcaps',
        'crc_basedata',
    ];

    protected $casts = [
        'gmspeed' => 'boolean',
        'invulnerable' => 'boolean',
        'ignore_tells' => 'boolean',
        'revoked' => 'boolean',
        'hideme' => 'boolean',
        'suspendeduntil' => 'datetime',
    ];

    public array $sortable = [
        'id',
        'name',
        'status',
        'time_creation'
    ];

    public function character(): BelongsTo
    {
        return $this->belongsTo(CharacterData::class, 'charname', 'name')
            ->select('id', 'name', 'class', 'level');
    }

    public function characters(): HasMany
    {
        return $this->hasMany(CharacterData::class, 'account_id');
    }

    public function ips(): HasMany
    {
        return $this->hasMany(AccountIp::class, 'accid')
            ->orderBy('lastused', 'desc');
    }

    public function sharedBank(): HasMany
    {
        return $this->hasMany(SharedBank::class, 'account_id');
    }

    public function rewards(): HasMany
    {
        return $this->hasMany(AccountReward::class, 'account_id');
    }

    public function gmIps(): HasMany
    {
        return $this->hasMany(GmIp::class, 'account_id');
    }
}
