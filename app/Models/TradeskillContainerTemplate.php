<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class TradeskillContainerTemplate extends BaseModel
{
    protected $fillable = [
        'name',
        'skill',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(TradeskillContainerTemplateItem::class);
    }
}
