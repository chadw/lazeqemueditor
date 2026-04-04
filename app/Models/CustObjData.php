<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustObjData extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'cust_obj_data';

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class, 'zonesn', 'short_name');
    }
}
