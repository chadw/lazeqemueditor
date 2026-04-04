<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class ParcelFilter
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

        // item (id or name)
        if ($val = trim((string) $this->request->input('item', ''))) {
            if (is_numeric($val)) {
                $query->where('item_id', (int) $val);
            } else {
                $query->whereHas('item', function (Builder $q) use ($val) {
                    $q->where('Name', 'like', '%' . $val . '%');
                });
            }
        }

        return $query;
    }
}
