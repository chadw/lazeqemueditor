<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class CharacterExpeditionLockoutFilter
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $query): Builder
    {
        if ($val = trim((string) $this->request->input('char', ''))) {
            if (is_numeric($val)) {
                $query->where('character_id', (int) $val);
            } else {
                $query->whereHas('character', function (Builder $q) use ($val) {
                    $q->where('name', 'like', '%' . $val . '%');
                });
            }
        }

        if ($val = trim((string) $this->request->input('expedition', ''))) {
            $query->where('expedition_name', 'like', '%' . $val . '%');
        }

        return $query;
    }
}
