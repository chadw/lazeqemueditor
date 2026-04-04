<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use App\Models\LootTable;
use App\Models\GlobalLoot;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\GlobalLootRequest;

class GlobalLootController extends Controller
{
    public function index()
    {
        $globalLoot = GlobalLoot::with('loottable')
            ->orderBy('id', 'asc')
            ->paginate(50);

        $zones = Zone::selectZones();
        $loottableId = LootTable::max('id');

        return view('global-loot.index', compact(
            'globalLoot',
            'zones',
            'loottableId'
        ));
    }

    public function edit(GlobalLoot $globalLoot)
    {
        $globalLoot->load([
            'loottable' => function ($query) {
                $query->with([
                    'loottableEntries' => function ($q) {
                        $q->with([
                            'lootdrop' => function ($qd) {
                                $qd->with([
                                    'entries' => function ($qe) {
                                        $qe->with('item');
                                    },
                                ]);
                            },
                        ]);
                    },
                ]);
            },
        ]);

        if ($globalLoot->loottable) {
            $globalLoot->loottable->loadCount(['globalLoot', 'npcs']);
        }

        $zones = Zone::selectZones();

        return view('global-loot.edit', compact('globalLoot', 'zones'));
    }

    public function store(GlobalLootRequest $request)
    {
        $globalLoot = DB::connection('eqemu')->transaction(function () use ($request) {
            $defaultName = 'GLB System Created (' . now()->format('Y-m-d') . ')';

            $lootTable = LootTable::create([
                'name' => $request->description ?? $defaultName,
            ]);

            $data = $request->validated();
            $data['description'] = $data['description'] ?? $defaultName;
            $data['loottable_id'] = $lootTable->id;

            return GlobalLoot::create($data);
        });

        toast()->success('Saved!', 'Global Loot created.');

        return response()->json([
            'success' => true,
            'data'    => $globalLoot,
            'redirect'=> route('global-loot.index'),
        ], 201);
    }

    public function update(GlobalLootRequest $request, GlobalLoot $globalLoot)
    {
        $globalLoot->update($request->validated());

        toast()->success('Saved!', 'Global Loot updated.');

        return response()->json([
            'success' => true,
            'data'    => $globalLoot,
            'redirect'=> url()->previous(),
        ], 201);
    }

    public function destroy(GlobalLoot $globalLoot)
    {
        $globalLoot->delete();

        return back()->with('success', __('Global Loot removed'));
    }
}
