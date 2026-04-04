<?php

namespace App\Models;

use Spatie\Activitylog\Contracts\Activity;

class DbStr extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'db_str';
    protected $primaryKey = null;
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'type',
        'value',
    ];

    public function tapActivity(Activity $activity, string $eventName)
    {
        $activity->subject_id = "{$this->type}-{$this->id}";
        parent::tapActivity($activity, $eventName);
    }

    public function getKey()
    {
        return "{$this->type}-{$this->id}";
    }

    public function getKeyName()
    {
        return 'type';
    }

    protected function setKeysForSaveQuery($query)
    {
        return $query->where('type', $this->getAttribute('type'))
            ->where('id', $this->getAttribute('id'));
    }

    protected function setKeysForSelectQuery($query)
    {
        return $query->where('type', $this->getAttribute('type'))
            ->where('id', $this->getAttribute('id'));
    }

    public function getSpellDescription(Spell $spell): string
    {
        // #1 Base for effect id 1
        // $1 Limit for effect id 1
        // @1 Max for effect id 1
        // %z (# ticks)
        $desc = $this->value ?? '';

        $desc = str_replace('%z', '(' . $spell->buffduration . ' ticks)', $desc);

        $desc = preg_replace_callback('/#(\d+)/', function ($matches) use ($spell) {
            return abs($spell->{'effect_base_value' . $matches[1]}) ?? '';
        }, $desc);

        $desc = preg_replace_callback('/\$(\d+)/', function ($matches) use ($spell) {
            return abs($spell->{'effect_limit_value' . $matches[1]}) ?? '';
        }, $desc);

        $desc = preg_replace_callback('/@(\d+)/', function ($matches) use ($spell) {
            return abs($spell->{'max' . $matches[1]}) ?? '';
        }, $desc);

        return $desc;
    }
}
