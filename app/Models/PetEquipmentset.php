<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class PetEquipmentset extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'pets_equipmentset';
    protected $primaryKey = 'set_id';
    public $timestamps = false;

    protected $fillable = [
        'set_id',
        'setname',
        'nested_set',
    ];

    public function petEquipmentsetEntries(): HasMany
    {
        return $this->hasMany(PetEquipmentsetEntry::class, 'set_id', 'set_id');
    }
}
