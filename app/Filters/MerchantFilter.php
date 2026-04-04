<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class MerchantFilter
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $query): Builder
    {
        if ($val = trim((string) $this->request->input('merchant', ''))) {
            if (is_numeric($val)) {
                $int = intval($val);
                $query->where(function (Builder $q) use ($int) {
                    $q->where('merchantid', $int)
                      ->orWhereHas('npc', function (Builder $qq) use ($int) {
                          $qq->where('id', $int)
                             ->where('merchant_id', '>', 0);
                      });
                });
            } else {
                $query->whereHas('npc', function (Builder $q) use ($val) {
                    $q->where('merchant_id', '>', 0)
                      ->where('name', 'like', '%' . $val . '%');
                });
            }
        }

        return $query;
    }
}
