<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class AaAbilityFilter
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $query): Builder
    {
        $this->ability($query, $this->request->input('ability'));
        $this->classes($query, $this->request->input('classes'));

        return $query;
    }

    protected function ability(Builder $query, $value): void
    {
        if (!$value) return;

        $value = trim((string) $value);

        if (is_numeric($value)) {
            $query->where('id', (int) $value);
            return;
        }

        $query->where('name', 'like', "%{$value}%");
    }

    protected function classes(Builder $query, $values): void
    {
        if (!$values) return;

        if (!is_array($values)) {
            $values = array_filter(array_map('trim', explode(',', (string) $values)));
        }

        foreach ($values as $v) {
            $bit = (int) $v;
            if (!$bit) continue;
            $query->whereRaw('classes & ? != 0', [$bit]);
        }
    }
}
