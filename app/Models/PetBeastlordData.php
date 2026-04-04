<?php

namespace App\Models;

class PetBeastlordData extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'pets_beastlord_data';
    protected $primaryKey = 'player_race';
    public $timestamps = false;

    protected $fillable = [
        'player_race',
        'pet_race',
        'texture',
        'helm_texture',
        'gender',
        'size_modifier',
        'face',
    ];
}
