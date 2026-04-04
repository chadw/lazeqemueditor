<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Kyslik\ColumnSortable\Sortable;

class Pet extends BaseModel
{
    use Sortable;

    protected $connection = 'eqemu';
    protected $table = 'pets';
    public $timestamps = false;

    protected $fillable = [
        'type',
        'petpower',
        'npcID',
        'temp',
        'petcontrol',
        'petnaming',
        'monsterflag',
        'equipmentset',
    ];

    protected $casts = [
        'monsterflag' => 'boolean',
        'temp' => 'boolean',
    ];

    public array $sortable = [
        'level',
        'type',
        'petpower',
        'hp',
        'ac',
    ];

    public function levelSortable($query, $direction)
    {
        return $query->leftJoin('npc_types as nt', 'nt.id', '=', 'pets.npcID')
            ->orderBy('nt.level', $direction)
            ->select('pets.*');
    }

    public function hpSortable($query, $direction)
    {
        return $query->leftJoin('npc_types as nt', 'nt.id', '=', 'pets.npcID')
            ->orderBy('nt.hp', $direction)
            ->select('pets.*');
    }

    public function acSortable($query, $direction)
    {
        return $query->leftJoin('npc_types as nt', 'nt.id', '=', 'pets.npcID')
            ->orderBy('nt.AC', $direction)
            ->select('pets.*');
    }

    public function npc(): BelongsTo
    {
        return $this->belongsTo(NpcType::class, 'npcID');
    }

    public function equipment(): HasOne
    {
        return $this->hasOne(PetEquipmentset::class, 'set_id', 'equipmentset');
    }

    public function scopeHasAnyEffect($query, array $effectIds)
    {
        return $query->where(function ($q) use ($effectIds) {
            foreach (range(1, 12) as $i) {
                $q->orWhereIn("effectid{$i}", $effectIds);
            }
        });
    }
}
