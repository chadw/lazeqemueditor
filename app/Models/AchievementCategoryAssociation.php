<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AchievementCategoryAssociation extends BaseModel
{
    protected $connection = 'eqemu';

    protected $table = 'achievement_category_associations';

    protected $primaryKey = null;

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'category_id',
        'sequence',
        'achievement_id',
        'display_text',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(AchievementCategory::class, 'category_id', 'id');
    }

    public function achievement(): BelongsTo
    {
        return $this->belongsTo(Achievement::class, 'achievement_id', 'id');
    }

    public function getKey()
    {
        return "{$this->category_id}:{$this->achievement_id}";
    }

    public function getKeyName()
    {
        return 'category_id';
    }

    protected function setKeysForSaveQuery($query)
    {
        return $query
            ->where('category_id', $this->getAttribute('category_id'))
            ->where('achievement_id', $this->getAttribute('achievement_id'));
    }

    protected function setKeysForSelectQuery($query)
    {
        return $query
            ->where('category_id', $this->getAttribute('category_id'))
            ->where('achievement_id', $this->getAttribute('achievement_id'));
    }
}
