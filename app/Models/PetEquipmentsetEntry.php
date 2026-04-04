<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\Contracts\Activity;

class PetEquipmentsetEntry extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'pets_equipmentset_entries';
    protected $primaryKey = 'set_id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'set_id',
        'slot',
        'item_id',
    ];

    public function petEquipment(): BelongsTo
    {
        return $this->belongsTo(PetEquipmentset::class, 'set_id');
    }

    public function item(): HasOne
    {
        return $this->hasOne(Item::class, 'id', 'item_id')
            ->select('id', 'Name', 'icon');
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        $activity->subject_id = "{$this->set_id}-{$this->slot}";
        parent::tapActivity($activity, $eventName);
    }

    public function getKey()
    {
        return "{$this->set_id}-{$this->slot}";
    }

    protected function setKeysForSaveQuery($query)
    {
        $setId = $this->getOriginal('set_id') ?? $this->getAttribute('set_id');
        $slot = $this->getOriginal('slot') ?? $this->getAttribute('slot');

        return $query->where('set_id', $setId)
            ->where('slot', $slot);
    }

    protected function setKeysForSelectQuery($query)
    {
        $setId = $this->getOriginal('set_id') ?? $this->getAttribute('set_id');
        $slot = $this->getOriginal('slot') ?? $this->getAttribute('slot');

        return $query->where('set_id', $setId)
            ->where('slot', $slot);
    }
}
