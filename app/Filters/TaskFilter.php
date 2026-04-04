<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class TaskFilter
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $query): Builder
    {
        // task name
        if ($val = trim((string) $this->request->input('name', ''))) {
            $query->where(function ($q) use ($val) {
                if (is_numeric($val)) {
                    $q->orWhere('id', (int) $val);
                }

                $q->orWhere('title', 'like', "%{$val}%");
            });
        }

        return $query;
    }
}
