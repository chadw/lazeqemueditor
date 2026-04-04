<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class GuildFilter
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $query): Builder
    {
        // name
        if ($val = trim((string) $this->request->input('name', ''))) {
            $query->where('name', 'like', "%{$val}%");
        }

        return $query;
    }
}
