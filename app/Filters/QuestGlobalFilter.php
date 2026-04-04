<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class QuestGlobalFilter
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $query): Builder
    {
        // key/name
        if ($val = $this->request->input('name')) {
            $query->where('name', 'like', '%' . (string) $val . '%');
        }

        // character (id or name)
        if ($val = trim((string) $this->request->input('character', ''))) {
            $query->where(function ($q) use ($val) {

                if (is_numeric($val)) {
                    $q->orWhere('charid', (int) $val);
                }

                $q->orWhereHas('character', function ($cq) use ($val) {
                    $cq->where('name', 'like', "%{$val}%");
                });
            });
        }

        // npc (id or name)
        if ($val = trim((string) $this->request->input('npc', ''))) {
            $query->where(function ($q) use ($val) {

                if (is_numeric($val)) {
                    $q->orWhere('npcid', (int) $val);
                }

                $q->orWhereHas('npc', function ($cq) use ($val) {
                    $cq->where('name', 'like', "%{$val}%");
                });
            });
        }

        // zone (id or short_name)
        if ($val = trim((string) $this->request->input('zone', ''))) {
            $query->where(function ($q) use ($val) {

                if (is_numeric($val)) {
                    $q->orWhere('zoneid', (int) $val);
                }

                $q->orWhereHas('zone', function ($cq) use ($val) {
                    $cq->where('short_name', 'like', "%{$val}%");
                });
            });
        }

        return $query;
    }
}
