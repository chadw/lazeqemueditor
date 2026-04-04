<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class DynamicZoneLockoutFilter
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $query): Builder
    {
        if ($val = trim((string) $this->request->input('dynamic_zone', ''))) {
            $query->whereHas('dz', function (Builder $q) use ($val) {
                $q->where('name', 'like', '%' . $val . '%');
            });
        }

        if ($val = trim((string) $this->request->input('event_name', ''))) {
            $query->where('event_name', 'like', '%' . $val . '%');
        }

        return $query;
    }
}
