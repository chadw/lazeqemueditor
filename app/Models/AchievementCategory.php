<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AchievementCategory extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'achievement_categories';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'parent_id',
        'sequence',
        'name',
        'description',
        'icon',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id')
            ->orderBy('sequence')
            ->orderBy('id');
    }

    public function associations(): HasMany
    {
        return $this->hasMany(AchievementCategoryAssociation::class, 'category_id', 'id')
            ->orderBy('sequence')
            ->orderBy('achievement_id');
    }

    public function achievements(): BelongsToMany
    {
        return $this->belongsToMany(
            Achievement::class,
            'achievement_category_associations',
            'category_id',
            'achievement_id'
        )
            ->withPivot(['sequence', 'display_text'])
            ->orderByPivot('sequence')
            ->orderBy('achievements.id');
    }
}
