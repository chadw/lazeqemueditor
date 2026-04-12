<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class LootTableFilter
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $query): Builder
    {
        if ($val = trim((string) $this->request->input('table', ''))) {
            if (is_numeric($val)) {
                $query->where('id', intval($val));
            } else {
                $query->where('name', 'like', '%' . $val . '%');
            }
        }

        if ($val = trim((string) $this->request->input('item', ''))) {
            if (is_numeric($val)) {
                $query->whereHas('loottableEntries.lootdropEntries', function ($q) use ($val) {
                    $q->where('item_id', intval($val));
                });
            } else {
                $query->whereHas('loottableEntries.lootdropEntries.item', function ($q) use ($val) {
                    $q->where('Name', 'like', '%' . $val . '%');
                });
            }
        }

        return $query;
    }
}
