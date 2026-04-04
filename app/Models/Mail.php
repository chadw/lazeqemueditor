<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mail extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'mail';
    protected $primaryKey = 'msgid';
    public $timestamps = false;

    public function character(): BelongsTo
    {
        return $this->belongsTo(CharacterData::class, 'charid')
            ->select('id', 'name');
    }
}
