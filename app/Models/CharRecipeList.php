<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharRecipeList extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'char_recipe_list';
    public $timestamps = false;

    protected $fillable = [
        'char_id',
        'recipe_id',
        'madecount',
    ];

    public function character(): BelongsTo
    {
        return $this->belongsTo(CharacterData::class, 'char_id')
            ->select('id', 'name');
    }

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(TradeskillRecipe::class, 'recipe_id')
            ->select('id', 'name');
    }
}
