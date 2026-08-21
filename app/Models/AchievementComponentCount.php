<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class AchievementComponentCount extends BaseModel
{
    protected $connection = 'eqemu';

    protected $table = 'achievement_associations';

    protected $primaryKey = 'component_id';

    public $incrementing = false;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'component_id',
        'required_count',
    ];

    public function components(): HasMany
    {
        return $this->hasMany(AchievementComponent::class, 'component_id', 'component_id');
    }
}
