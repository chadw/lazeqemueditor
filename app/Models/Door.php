<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Door extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'doors';
    public $timestamps = false;

    protected $fillable = [
        'doorid',
        'zone',
        'version',
        'name',
        'pos_y',
        'pos_x',
        'pos_z',
        'heading',
        'opentype',
        'guild',
        'lockpick',
        'keyitem',
        'nokeyring',
        'triggerdoor',
        'triggertype',
        'disable_timer',
        'doorisopen',
        'door_param',
        'dest_zone',
        'dest_instance',
        'dest_x',
        'dest_y',
        'dest_z',
        'dest_heading',
        'invert_state',
        'incline',
        'size',
        'buffer',
        'client_version_mask',
        'is_ldon_door',
        'close_timer_ms',
        'dz_switch_id',
        'min_expansion',
        'max_expansion',
        'content_flags',
        'content_flags_disabled',
    ];

    protected $casts = [
        'doorisopen'    => 'boolean',
        'nokeyring'     => 'boolean',
        'is_ldon_door'  => 'boolean',
        'disable_timer' => 'boolean',
    ];

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class, 'zone', 'short_name')
            ->select('zoneidnumber', 'short_name', 'long_name');
    }

    public function key(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'keyitem', 'id')
            ->select('id', 'Name', 'icon');
    }
}
