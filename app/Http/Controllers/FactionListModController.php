<?php

namespace App\Http\Controllers;

use App\Models\FactionList;
use App\Models\FactionListMod;
use App\Http\Requests\FactionListModRequest;

class FactionListModController extends Controller
{
    public function index(FactionList $faction)
    {
        $mods = $faction->mod()->get();

        return view('factions.mods.index', compact('faction', 'mods'));
    }

    public function store(FactionListModRequest $request, FactionList $faction)
    {
        $faction->mod()->create($request->only(['mod', 'mod_name']));

        toast()->success('Saved!', 'Faction mod created.');

        return response()->json([
            'success' => true,
            'data'    => $faction->fresh(),
            'redirect'=> url()->previous(),
        ], 201);
    }

    public function update(FactionListModRequest $request, FactionList $faction, FactionListMod $mod)
    {
        $mod->update($request->only(['mod', 'mod_name']));

        toast()->success('Saved!', 'Faction mod updated.');

        return response()->json([
            'success' => true,
            'data'    => $mod->fresh(),
            'redirect'=> url()->previous(),
        ], 201);
    }

    public function destroy(FactionList $faction, FactionListMod $mod)
    {
        $mod->delete();

        toast()->success('Deleted!', 'Faction mod deleted.');

        return back();
    }
}
