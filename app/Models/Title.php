<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Title extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'titles';
    public $timestamps = false;

    protected $fillable = [
        'skill_id',
        'min_skill_value',
        'max_skill_value',
        'min_aa_points',
        'max_aa_points',
        'class',
        'gender',
        'char_id',
        'status',
        'item_id',
        'prefix',
        'suffix',
        'title_set',
    ];

    public function character(): BelongsTo
    {
        return $this->belongsTo(CharacterData::class, 'char_id')
            ->select('id', 'name');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id')
            ->select('id', 'Name', 'icon');
    }
}
