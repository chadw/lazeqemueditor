<?php

namespace App\Filters;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AccountFilter
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $query): Builder
    {
        // name or id
        if ($val = trim((string) $this->request->input('name', ''))) {
            $query->where(function ($q) use ($val) {
                if (is_numeric($val)) {
                    $q->orWhere('id', (int) $val);
                }

                $q->orWhere('name', 'like', "%{$val}%");
            });
        }

        // status
        if ($status = $this->request->input('status')) {
            $query->where('status', (int) $status);
        }

        // Date from
        if ($from = $this->request->input('from')) {
            $query->where('time_creation', '>=', Carbon::parse($from)->timestamp);
        }

        // Date to
        if ($to = $this->request->input('to')) {
            $query->where('time_creation', '<=', Carbon::parse($to)->timestamp);
        }

        return $query;
    }
}
