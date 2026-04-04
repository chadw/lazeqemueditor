<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PlayerEventLog;
use App\Filters\PlayerLogFilter;
use App\Models\AlternateCurrency;
use App\Services\PlayerLogService;

class PlayerLogController extends Controller
{
    public function index(Request $request, PlayerLogService $service, PlayerLogFilter $filter)
    {
        $perPage = 100;
        $page = $request->integer('page', 1);

        $baseQuery = PlayerEventLog::with(['character', 'account', 'zone'])
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        $filter->apply($baseQuery);

        $total = (clone $baseQuery)->count();

        if ($page <= 10) {
            $logs = $baseQuery->paginate($perPage);
        } else {
            $cursor = $this->getKeysetCursor($page, $perPage);

            if ($cursor) {
                $baseQuery->where(function ($q) use ($cursor) {
                    $q->where('created_at', '<', $cursor['created_at'])
                        ->orWhere(function ($q2) use ($cursor) {
                            $q2->where('created_at', $cursor['created_at'])
                                ->where('id', '<', $cursor['id']);
                        });
                });
            }

            $collection = $baseQuery->limit($perPage)->get();

            $logs = new \Illuminate\Pagination\LengthAwarePaginator(
                $collection,
                $total,
                $perPage,
                $page,
                [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ]
            );
        }

        $logs = $logs->withQueryString();
        $logs = $service->hydratePaginated($logs);

        $altCurrency = AlternateCurrency::allAltCurrency();

        return view('player-logs.index', compact('logs', 'altCurrency'));
    }

    protected function getKeysetCursor(int $page, int $perPage): ?array
    {
        $offset = ($page - 1) * $perPage;

        $row = PlayerEventLog::select('created_at', 'id')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->skip($offset)
            ->first();

        return $row ? ['created_at' => $row->created_at, 'id' => $row->id] : null;
    }

    public function destroy(PlayerEventLog $playerEventLog)
    {
        $playerEventLog->delete();

        return back()->with('success', 'Player Log deleted.');
    }
}
