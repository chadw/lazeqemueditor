<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class PlayerLogFilter
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $query): Builder
    {
        // Account (id or name)
        if ($val = trim((string) $this->request->input('account', ''))) {
            if (is_numeric($val)) {
                $query->where('account_id', (int) $val);
            } else {
                $query->whereHas('account', function (Builder $q) use ($val) {
                    $q->where('name', 'like', "%{$val}%");
                });
            }
        }

        // Character (id or name)
        if ($val = trim((string) $this->request->input('character', ''))) {
            if (is_numeric($val)) {
                $query->where('char_id', (int) $val);
            } else {
                $query->whereHas('character', function (Builder $q) use ($val) {
                    $q->where('name', 'like', "%{$val}%");
                });
            }
        }

        // Zone (id or name)
        if ($val = trim((string) $this->request->input('zone', ''))) {
            if (is_numeric($val)) {
                $query->where('zone_id', (int) $val);
            } else {
                $query->whereHas('zone', function (Builder $q) use ($val) {
                    $q->where('short_name', 'like', "%{$val}%")
                      ->orWhere('long_name', 'like', "%{$val}%");
                });
            }
        }

        // Event Type (select)
        if ($val = $this->request->input('event_type_id')) {
            $query->where('event_type_id', (int) $val);
        }

        return $query;
    }
}
