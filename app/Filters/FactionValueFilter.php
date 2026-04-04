<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class FactionValueFilter
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $query): Builder
    {
        // character (id or name)
        if ($val = trim((string) $this->request->input('char', ''))) {
            if (is_numeric($val)) {
                $query->where('char_id', (int) $val);
            } else {
                $query->whereHas('character', function (Builder $q) use ($val) {
                    $q->where('name', 'like', '%' . $val . '%');
                });
            }
        }

        // faction (id or name)
        if ($val = trim((string) $this->request->input('faction', ''))) {
            if (is_numeric($val)) {
                $query->where('faction_id', (int) $val);
            } else {
                $query->whereHas('faction', function (Builder $q) use ($val) {
                    $q->where('name', 'like', '%' . $val . '%');
                });
            }
        }

        return $query;
    }
}
