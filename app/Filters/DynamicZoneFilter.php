<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class DynamicZoneFilter
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $query): Builder
    {
        if ($val = trim((string) $this->request->input('dynamic_zone', ''))) {
            $query->where('name', 'like', '%' . $val . '%');
        }

        if ($val = trim((string) $this->request->input('leader', ''))) {
            if (is_numeric($val)) {
                $query->where('leader_id', intval($val));
            } else {
                $query->whereHas('leader', function (Builder $q) use ($val) {
                    $q->where('name', 'like', '%' . $val . '%');
                });
            }
        }

        return $query;
    }
}
