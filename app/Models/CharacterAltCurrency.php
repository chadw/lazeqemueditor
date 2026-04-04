<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Contracts\Activity;
use Kyslik\ColumnSortable\Sortable;

class CharacterAltCurrency extends BaseModel
{
    use Sortable;

    protected $connection = 'eqemu';
    protected $table = 'character_alt_currency';
    protected $primaryKey = null;
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'char_id',
        'currency_id',
        'amount',
    ];

    public array $sortable = [
        'char_id',
        'amount',
        'char_name'
    ];

    public function character(): BelongsTo
    {
        return $this->belongsTo(CharacterData::class, 'char_id')
            ->select('id', 'name');
    }

    public function altCurrency(): BelongsTo
    {
        return $this->belongsTo(AlternateCurrency::class, 'currency_id');
    }

    public function charNameSortable($query, $direction)
    {
        return $query
            ->join('character_data', 'character_data.id', '=', 'character_alt_currency.char_id')
            ->orderBy('character_data.name', $direction)
            ->select('character_alt_currency.*');
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        $activity->subject_id = "{$this->char_id}-{$this->currency_id}";
        parent::tapActivity($activity, $eventName);
    }

    public function getKey()
    {
        return "{$this->char_id}-{$this->currency_id}";
    }

    public function getKeyName()
    {
        return 'char_id';
    }

    protected function setKeysForSaveQuery($query)
    {
        return $query->where('char_id', $this->getAttribute('char_id'))
            ->where('currency_id', $this->getAttribute('currency_id'));
    }

    protected function setKeysForSelectQuery($query)
    {
        return $query->where('char_id', $this->getAttribute('char_id'))
            ->where('currency_id', $this->getAttribute('currency_id'));
    }
}
