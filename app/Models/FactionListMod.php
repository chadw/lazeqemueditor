<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;

class FactionListMod extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'faction_list_mod';
    public $timestamps = false;

    protected $fillable = [
        'faction_id',
        'mod',
        'mod_name',
    ];

    protected $appends = [
        'mod_type'
    ];

    protected function modType(): Attribute
    {
        return Attribute::make(
            get: function ($value, array $attributes) {
                $modName = $attributes['mod_name'] ?? $attributes['mod'] ?? null;
                if (empty($modName) || !is_string($modName)) {
                    return null;
                }

                $modName = trim($modName);
                $category = strtolower(substr($modName, 0, 1));
                $indexStr = substr($modName, 1);
                if ($indexStr === '' || !is_numeric($indexStr)) {
                    return null;
                }

                $index = (int) $indexStr;

                switch ($category) {
                    case 'r':
                        $list = (array) config('everquest.races');
                        $type = 'Race';
                        break;
                    case 'c':
                        $list = (array) config('everquest.classes');
                        $type = 'Class';
                        break;
                    case 'd':
                        $list = (array) config('everquest.deity');
                        $type = 'Deity';
                        break;
                    default:
                        return null;
                }

                $name = $list[$index] ?? null;
                if ($name === null) {
                    return null;
                }

                return [
                    'type' => $type,
                    'name' => "{$name} ({$index})",
                ];
            }
        );
    }
}
