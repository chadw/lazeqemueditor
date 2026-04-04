<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Contracts\Activity;

class BaseData extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'base_data';
    protected $primaryKey = null;
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'level',
        'class',
        'hp',
        'mana',
        'end',
        'hp_regen',
        'end_regen',
        'hp_fac',
        'mana_fac',
        'end_fac',
    ];

    public function tapActivity(Activity $activity, string $eventName)
    {
        $activity->subject_id = "{$this->level}-{$this->class}";
        parent::tapActivity($activity, $eventName);
    }

    public function getKey()
    {
        return "{$this->level}-{$this->class}";
    }

    protected function setKeysForSaveQuery($query): Builder
    {
        return $query->where('level', $this->getAttribute('level'))
            ->where('class', $this->getAttribute('class'));
    }

    protected function setKeysForSelectQuery($query): Builder
    {
        return $query->where('level', $this->getAttribute('level'))
            ->where('class', $this->getAttribute('class'));
    }
}
