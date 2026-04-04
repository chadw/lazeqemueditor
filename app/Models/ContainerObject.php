<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContainerObject extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'object';
    public $timestamps = false;

    protected $fillable = [
        'zoneid',
        'version',
        'xpos',
        'ypos',
        'zpos',
        'heading',
        'itemid',
        'charges',
        'objectname',
        'type',
        'icon',
        'size_percentage',
        'unknown24',
        'unknown60',
        'unknown64',
        'unknown68',
        'unknown72',
        'unknown76',
        'unknown84',
        'size',
        'solid_type',
        'incline',
        'tilt_x',
        'tilt_y',
        'display_name',
        'min_expansion',
        'max_expansion',
        'content_flags',
        'content_flags_disabled',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'itemid')
            ->select('id', 'Name', 'icon');
    }
}
