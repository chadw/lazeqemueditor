<?php

namespace App\Models;

use Spatie\Activitylog\Contracts\Activity;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Kyslik\ColumnSortable\Sortable;

class FactionValue extends BaseModel
{
    use Sortable;
    protected $connection = 'eqemu';
    protected $table = 'faction_values';
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'char_id',
        'faction_id',
        'current_value',
        'temp',
    ];

    public array $sortable = [
        'char_id',
        'char_name',
        'faction_name',
        'current_value',
    ];

    protected function standing(): Attribute
    {
        return Attribute::get(fn() => factionValue($this->current_value));
    }

    public function character(): BelongsTo
    {
        return $this->belongsTo(CharacterData::class, 'char_id')
            ->select('id', 'name');
    }

    public function faction(): BelongsTo
    {
        return $this->belongsTo(FactionList::class, 'faction_id');
    }

    public function charNameSortable($query, $direction)
    {
        return $query
            ->join('character_data', 'character_data.id', '=', 'faction_values.char_id')
            ->orderBy('character_data.name', $direction)
            ->select('faction_values.*');
    }

    public function factionNameSortable($query, $direction)
    {
        return $query
            ->join('faction_list', 'faction_list.id', '=', 'faction_values.faction_id')
            ->orderBy('faction_list.name', $direction)
            ->select('faction_values.*');
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        $activity->subject_id = "{$this->char_id}-{$this->faction_id}";
        parent::tapActivity($activity, $eventName);
    }

    public function getKey()
    {
        return "{$this->char_id}-{$this->faction_id}";
    }

    public function getKeyName()
    {
        return 'char_id';
    }

    protected function setKeysForSaveQuery($query)
    {
        return $query->where('char_id', $this->getAttribute('char_id'))
            ->where('faction_id', $this->getAttribute('faction_id'));
    }

    protected function setKeysForSelectQuery($query)
    {
        return $query->where('char_id', $this->getAttribute('char_id'))
            ->where('faction_id', $this->getAttribute('faction_id'));
    }
}
