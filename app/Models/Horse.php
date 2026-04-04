<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Kyslik\ColumnSortable\Sortable;

class Horse extends BaseModel
{
    use Sortable;

    protected $connection = 'eqemu';
    protected $table = 'horses';
    public $timestamps = false;

    protected $fillable = [
        'filename',
        'race',
        'gender',
        'texture',
        'helmtexture',
        'mountspeed',
        'notes',
    ];

    protected $casts = [
        'mountspeed' => 'float',
    ];

    public array $sortable = [
        'filename',
        'mountspeed',
    ];

    public function npc(): BelongsTo
    {
        return $this->belongsTo(NpcType::class, 'filename', 'name')
            ->select('id', 'name');
    }
}
