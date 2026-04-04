<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class DataBucketFilter
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $query): Builder
    {
        // key
        if ($val = trim((string) $this->request->input('key', ''))) {
            $query->where('key', 'like', "%{$val}%");
        }

        // character (id or name)
        if ($val = trim((string) $this->request->input('character', ''))) {
            $query->where(function ($q) use ($val) {

                if (is_numeric($val)) {
                    $q->orWhere('character_id', (int) $val);
                }

                $q->orWhereHas('character', function ($cq) use ($val) {
                    $cq->where('name', 'like', "%{$val}%");
                });
            });
        }

        return $query;
    }
}
