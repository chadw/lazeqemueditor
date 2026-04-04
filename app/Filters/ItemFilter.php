<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class ItemFilter
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $query): Builder
    {
        $this->item($query, $this->request->input('item'));
        $this->type($query, $this->request->input('type'));

        return $query;
    }

    protected function item(Builder $query, $value): void
    {
        if (!$value) {
            return;
        }

        $value = trim($value);

        $query->where(function ($q) use ($value) {
            if (is_numeric($value)) {
                $q->orWhere('id', (int) $value);
            }
            $q->orWhere('Name', 'like', "%{$value}%");
        });
    }

    protected function type(Builder $query, $value): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $value = (int) $value;
        $bagSlots = (int) ($this->request->input('bagslots') ?? 1);

        if ($value === 7 || $value === 19) {
            // throwing
            $query->whereIn('itemtype', [7, 19]);
        } elseif ($value === 56 || $value === 64) {
            // augment distillers
            $query->whereIn('itemtype', [56, 64]);
        } elseif ($value === 33 || $value === 39) {
            // keys
            $query->whereIn('itemtype', [33, 39]);
        } elseif ($value === 555) {
            // custom bag filter
            $query->where('bagslots', '>=', $bagSlots)
                ->whereIn('bagtype', [0,1,2,3,4,5,6,7]);
        } elseif ($value === 556) {
            // quest bags
            $query->where('bagslots', '>=', $bagSlots)
                ->where('bagtype', 13);
        } elseif ($value === 557) {
            // tradeskill bags
            $query->where('bagslots', '>=', $bagSlots)
                ->where('bagtype', '>=', 9)
                ->where('bagtype', '!=', 13);
        } else {
            $query->where('itemtype', $value);
        }
    }
}
