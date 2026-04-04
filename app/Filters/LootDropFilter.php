<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class LootDropFilter
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $query): Builder
    {
        if ($val = trim((string) $this->request->input('drop', ''))) {
            if (is_numeric($val)) {
                $query->where('id', intval($val));
            } else {
                $query->where('name', 'like', '%' . $val . '%');
            }
        }

        if ($this->request->boolean('orphan')) {
            $query->whereDoesntHave('loottableEntries');
        }

        return $query;
    }
}
