<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class CharacterAltCurrencyFilter
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $query): Builder
    {
        // character (id or name)
        if ($val = trim((string) $this->request->input('character', ''))) {
            $query->whereHas('character', function ($q) use ($val) {
                if (is_numeric($val)) {
                    $q->where('id', $val);
                } else {
                    $q->where('name', 'like', "%{$val}%");
                }
            });
        }

        // alt currency type
        $currency = $this->request->input('currency');
        if ($currency !== null && $currency !== '') {
            $query->where('currency_id', (int) $currency);
        }

        return $query;
    }
}
