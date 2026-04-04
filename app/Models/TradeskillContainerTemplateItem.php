<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TradeskillContainerTemplateItem extends BaseModel
{
    protected $fillable = [
        'container_template_id',
        'item_id',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(TradeskillContainerTemplate::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id', 'id')
            ->select('id', 'Name', 'icon');
    }
}
