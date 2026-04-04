<?php

namespace App\Models;

class FactionBaseData extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'faction_base_data';
    protected $primaryKey = 'client_faction_id';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'client_faction_id',
        'min',
        'max',
        'unk_hero1',
        'unk_hero2',
        'unk_hero3',
    ];
}
