<?php

namespace App\Models;

use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Kyslik\ColumnSortable\Sortable;

class CharacterExpeditionLockout extends BaseModel
{
    use Sortable;

    protected $connection = 'eqemu';
    protected $table = 'character_expedition_lockouts';
    public $timestamps = false;

    protected $fillable = [
        'character_id',
        'expedition_name',
        'event_name',
        'expire_time',
        'duration',
        'from_expedition_uuid',
    ];

    protected $casts = [
        'expire_time' => 'datetime',
    ];

    public array $sortable = [
        'character_name',
        'expedition_name',
        'event_name',
        'expire_time',
    ];

    protected function cleanDuration(): Attribute
    {
        return Attribute::get(function () {
            return CarbonInterval::seconds($this->duration)->cascade();
        });
    }

    public function character(): BelongsTo
    {
        return $this->belongsTo(CharacterData::class, 'character_id');
    }

    public function characterNameSortable($query, $direction)
    {
        return $query
            ->join('character_data', 'character_data.id', '=', 'character_expedition_lockouts.character_id')
            ->orderBy('character_data.name', $direction)
            ->select('character_expedition_lockouts.*');
    }
}
