<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class TraderAuditFilter
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $query): Builder
    {
        // Seller (id or name)
        if ($val = trim((string) $this->request->input('seller', ''))) {
            $query->where(function ($q) use ($val) {
                if (is_numeric($val)) {
                    $q->orWhereHas('sellerCharacter', function ($sq) use ($val) {
                        $sq->where('id', (int) $val);
                    });
                }

                $q->orWhereHas('sellerCharacter', function ($sq) use ($val) {
                    $sq->where('name', 'like', "%{$val}%");
                });

                $q->orWhere('seller', 'like', "%{$val}%");
            });
        }

        // Buyer (id or name)
        if ($val = trim((string) $this->request->input('buyer', ''))) {
            $query->where(function ($q) use ($val) {
                if (is_numeric($val)) {
                    $q->orWhereHas('buyerCharacter', function ($bq) use ($val) {
                        $bq->where('id', (int) $val);
                    });
                }

                $q->orWhereHas('buyerCharacter', function ($bq) use ($val) {
                    $bq->where('name', 'like', "%{$val}%");
                });

                $q->orWhere('buyer', 'like', "%{$val}%");
            });
        }

        // Item (id or name)
        if ($val = trim((string) $this->request->input('item', ''))) {
            $query->where(function ($q) use ($val) {
                if (is_numeric($val)) {
                    $q->orWhere('item_id', (int) $val);
                }

                $q->orWhereHas('item', function ($iq) use ($val) {
                    $iq->where('name', 'like', "%{$val}%");
                });

                $q->orWhere('itemname', 'like', "%{$val}%");
            });
        }

        // Date from
        if ($from = $this->request->input('from')) {
            $query->where('time', '>=', $from);
        }

        // Date to
        if ($to = $this->request->input('to')) {
            $query->where('time', '<=', $to);
        }

        return $query;
    }
}
