<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TraderAudit;
use App\Filters\TraderAuditFilter;

class TraderAuditController extends Controller
{
    public function index(Request $request) {
        $perPage = 100;

        $logs = (new TraderAuditFilter($request))
            ->apply(TraderAudit::query())
            ->with(['item', 'sellerCharacter', 'buyerCharacter'])
            ->when(request('item'), function ($query, $item) {
                $query->whereHas('item', function ($q) use ($item) {
                    $q->where('name', 'like', "%{$item}%");
                });
            })
            ->sortable(['time' => 'desc'])
            ->paginate($perPage)
            ->withQueryString();

        return view('trader-audit.index', compact('logs'));
    }

    public function destroy(Request $request)
    {
        TraderAudit::where('time', $request->time)
            ->where('seller', $request->seller)
            ->where('buyer', $request->buyer)
            ->where('itemname', $request->itemname)
            ->delete();

        return back()->with('success', 'Trader audit entry deleted.');
    }
}
