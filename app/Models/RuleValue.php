<?php

namespace App\Models;

class RuleValue extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'rule_values';
    protected $primaryKey = 'rule_name';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected static $merchantMods = null;

    protected $fillable = [
        'rule_value',
    ];

    public function getRouteKeyName()
    {
        return 'rule_name';
    }

    public static function getMerchantMods(): object
    {
        if (static::$merchantMods === null) {
            static::$merchantMods = (object) [
                'buy'  => (float) self::where('rule_name', 'Merchant:BuyCostMod')->value('rule_value') ?: 0.95,
                'sell' => (float) self::where('rule_name', 'Merchant:SellCostMod')->value('rule_value') ?: 1.05,
            ];
        }

        return static::$merchantMods;
    }
}
