<?php

namespace App\Http\Controllers;

use App\Http\Requests\FactionListRequest;
use App\Models\FactionList;
use App\Models\NpcType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FactionController extends Controller
{
    public function index(Request $request)
    {
        $factions = FactionList::orderBy('name', 'asc')
            ->paginate(50)
            ->withQueryString();

        return view('factions.index', compact('factions'));
    }

    public function edit(Request $request)
    {
        $factionId  = (int) $request->query('faction');

        $factions = FactionList::selectFactions();

        // no faction selected
        if (!$factionId) {
            return view('factions.edit', [
                'factions'           => $factions,
                'faction'            => null,
                'selectedFactionId'  => null,
            ]);
        }

        // faction selected
        $faction = FactionList::where('id', $factionId)
            ->with([
                'basedata',
                'mod',
            ])
            ->first();

        abort_unless($faction, 404);

        return view('factions.edit', [
            'factions'           => $factions,
            'faction'            => $faction,
            'selectedFactionId'  => $factionId,
        ]);
    }

    public function store(FactionListRequest $request)
    {
        $faction = DB::transaction(function () use ($request) {
            $maxId = (int) FactionList::max('id');
            $nextId = $maxId + 1;

            $data = array_merge(['id' => $nextId], $request->only(['name', 'base']));

            $f = FactionList::create($data);

            $baseData = $request->input('faction_base_data', []);

            $values = collect($baseData)
                ->except('client_faction_id')
                ->map(fn($v) => $v !== '' ? $v : null)
                ->all();

            if (!empty(array_filter($values, fn($v) => $v !== null))) {
                $f->basedata()->updateOrCreate(
                    ['client_faction_id' => $f->id],
                    $values
                );
            }

            return $f;
        });

        toast()->success('Created', "Created Faction [{$faction->name}].");

        return response()->json([
            'success' => true,
            'data'    => $faction->fresh(),
            'redirect'=> route('factions.edit', ['faction' => $faction->id]),
        ], 200);
    }

    public function update(FactionListRequest $request, FactionList $faction)
    {
        DB::transaction(function () use ($request, $faction) {
            $faction->update(
                $request->only(['name', 'base'])
            );

            $baseData = $request->input('faction_base_data', []);

            $values = collect($baseData)
                ->except('client_faction_id')
                ->map(fn($v) => $v !== '' ? $v : null)
                ->all();

            if (empty(array_filter($values, fn($v) => $v !== null))) {
                $faction->basedata()->delete();
            } else {
                $faction->basedata()->updateOrCreate(['client_faction_id' => $faction->id], $values);
            }
        });

        toast()->success('Saved!', "Faction {$faction->name} updated.");

        if (!$request->factions) {
            return response()->json([
                'success' => true,
                'data'    => $faction->fresh(),
                'redirect'=> url()->previous(),
            ], 200);
        }

        return back();
    }

    public function destroy(FactionList $faction)
    {
        $name = $faction->name ?? $faction->id;

        DB::transaction(function () use ($faction) {
            $faction->basedata()->delete();
            $faction->mod()->delete();
            $faction->delete();
        });

        toast()->success('Deleted', 'Deleted Faction [' . $name . '] and related records.');

        return redirect()->route('factions.edit');
    }

    public function npcsOnFaction(FactionList $faction)
    {
        $npcs = NpcType::select('id', 'name', 'level', 'race', 'class', 'hp', 'maxlevel', 'version', 'npc_faction_id')
            ->with('primaryFaction', 'firstSpawnEntries.spawn2.zoneData')
            ->whereHas(
                'primaryFaction',
                fn($q) =>
                $q->where('primaryfaction', $faction->id)
            )
            ->orderBy('name')
            ->paginate(50);

        return view('factions.npcs', [
            'faction' => $faction,
            'npcs' => $npcs,
            'type' => 'primary',
        ]);
    }

    public function npcsByFactionEffect(FactionList $faction, string $effect)
    {
        $operator = match ($effect) {
            'increase' => '>',
            'decrease' => '<',
            'nochange' => '=',
            default => null,
        };

        abort_if(is_null($operator), 404);

        $npcs = NpcType::select('id', 'name', 'level', 'race', 'class', 'hp', 'maxlevel', 'version', 'npc_faction_id')
            ->with([
                'factionEntries' => fn($q) => $q->where('faction_id', $faction->id),
                'firstSpawnEntries.spawn2.zoneData',
            ])
            ->whereHas(
                'factionEntries',
                fn($q) =>
                $q->where('faction_id', $faction->id)->where('npc_value', $operator, 0)
            )
            ->orderBy('name')
            ->paginate(50);

        return view('factions.npcs', [
            'faction' => $faction,
            'npcs' => $npcs,
            'type' => $effect,
        ]);
    }

    public function search(Request $request)
    {
        $search = $request->string('q');

        return FactionList::query()
            ->select('id', 'name')
            ->when($search, function ($q) use ($search) {
                $q->where('id', $search)
                    ->orWhere('name', 'like', "%{$search}%");
            })
            ->orderBy('id')
            ->limit(50)
            ->get();
    }

    public function options()
    {
        return FactionList::options();
    }
}
