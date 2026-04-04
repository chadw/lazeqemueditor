<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class BookFilter
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $query): Builder
    {
        if ($val = trim((string) $this->request->input('name', ''))) {
            $query->where('name', 'like', '%' . $val . '%');
        }

        if ($val = trim((string) $this->request->input('item', ''))) {
            $query->whereHas('item', function (Builder $q) use ($val) {
                if (is_numeric($val)) {
                    $q->where('id', (int) $val)
                      ->orWhere('Name', 'like', '%' . $val . '%');
                    return;
                }

                $q->where('Name', 'like', '%' . $val . '%');
            });
        }

        return $query;
    }
}
