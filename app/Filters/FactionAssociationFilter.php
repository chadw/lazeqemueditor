<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class FactionAssociationFilter
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $query): Builder
    {
        if ($val = trim((string) $this->request->input('faction', ''))) {
            $query->whereHas('factionList', function (Builder $q) use ($val) {
                $q->where('name', 'like', '%' . $val . '%');
            });
        }

        return $query;
    }
}
