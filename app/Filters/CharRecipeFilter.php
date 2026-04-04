<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class CharRecipeFilter
{
    public function apply(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['character'] ?? null, function ($q, $value) {
                $q->whereHas('character', function ($q) use ($value) {
                    $q->where('id', $value)
                      ->orWhere('name', 'like', "%{$value}%");
                });
            })
            ->when($filters['recipe'] ?? null, function ($q, $value) {
                $q->whereHas('recipe', function ($q) use ($value) {
                    $q->where('id', $value)
                      ->orWhere('name', 'like', "%{$value}%");
                });
            });
    }
}
