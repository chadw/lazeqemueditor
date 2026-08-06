<?php

namespace App\Http\Controllers;

use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AchievementLookupController extends Controller
{
    /**
     * Search the EQEmu records referenced by achievement criteria and rewards.
     */
    public function __invoke(Request $request, string $type): JsonResponse
    {
        $input = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'ids' => ['nullable', 'string', 'max:1200'],
        ]);
        $search = trim((string) ($input['q'] ?? ''));
        $ids = collect(explode(',', (string) ($input['ids'] ?? '')))
            ->filter(fn (string $id) => ctype_digit($id))
            ->map(fn (string $id) => (int) $id)
            ->unique()
            ->take(100)
            ->values();

        $query = $this->queryFor($type);

        if ($ids->isNotEmpty()) {
            $query->whereIn('lookup_id', $ids);
        } elseif ($search !== '') {
            $query->where(function (Builder $query) use ($search) {
                if (ctype_digit($search)) {
                    $query->where('lookup_id', (int) $search)
                        ->orWhere('lookup_name', 'like', "%{$search}%");
                } else {
                    $query->where('lookup_name', 'like', "%{$search}%");
                }
            });
        }

        $results = $query
            ->orderBy('lookup_id')
            ->limit(50)
            ->get()
            ->map(function (object $row) {
                $id = (int) $row->lookup_id;
                $result = [
                    'id' => $id,
                    'name' => trim((string) $row->lookup_name) ?: "Record {$id}",
                ];

                if (isset($row->icon)) {
                    $result['icon'] = (int) $row->icon;
                }

                return $result;
            });

        return response()->json($results);
    }

    private function queryFor(string $type): Builder
    {
        $connection = DB::connection('eqemu');

        $base = match ($type) {
            'achievement' => $connection->table('achievements')
                ->selectRaw('id AS lookup_id, name AS lookup_name'),
            'npc' => $connection->table('npc_types')
                ->selectRaw('id AS lookup_id, name AS lookup_name'),
            'task' => $connection->table('tasks')
                ->selectRaw('id AS lookup_id, title AS lookup_name'),
            'zone' => $connection->table('zone')
                ->selectRaw('zoneidnumber AS lookup_id, long_name AS lookup_name')
                ->where('version', 0),
            'item' => $connection->table('items')
                ->selectRaw('id AS lookup_id, Name AS lookup_name, icon'),
            'recipe' => $connection->table('tradeskill_recipe')
                ->selectRaw('id AS lookup_id, name AS lookup_name'),
            'currency' => $connection->table('alternate_currency AS currency')
                ->leftJoin('items AS item', 'item.id', '=', 'currency.item_id')
                ->selectRaw('currency.id AS lookup_id, item.Name AS lookup_name'),
            'title-set' => $connection->table('titles')
                ->selectRaw("title_set AS lookup_id, MAX(CASE WHEN prefix <> '' THEN prefix WHEN suffix <> '' THEN suffix ELSE 'Title set' END) AS lookup_name")
                ->where('title_set', '>', 0)
                ->groupBy('title_set'),
            default => abort(404),
        };

        // Wrap the source query so its normalized aliases can safely be used
        // by the common search and exact-ID filters on MySQL and SQLite.
        return $connection->query()->fromSub($base, 'achievement_lookup')->select('*');
    }
}
