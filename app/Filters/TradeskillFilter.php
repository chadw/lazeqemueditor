<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class TradeskillFilter
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $query): Builder
    {
        // recipe name
        if ($val = trim((string) $this->request->input('recipe_name', ''))) {
            $query->where('name', 'like', "%{$val}%");
        }

        // item id or name
        if ($val = trim((string) $this->request->input('item', ''))) {
            if (is_numeric($val)) {
                $query->whereHas('entries', function ($q) use ($val) {
                    $q->where('item_id', (int) $val);
                });
            } else {
                $query->whereHas('entries', function ($q) use ($val) {
                    $q->whereHas('item', function ($q2) use ($val) {
                        $q2->where('Name', 'like', "%{$val}%");
                    });
                });
            }
        }

        // tradeskill
        if ($ts = $this->request->input('ts')) {
            $query->where('tradeskill', (int) $ts);
        }

        return $query;
    }
}
