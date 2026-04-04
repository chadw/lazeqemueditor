<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use App\Models\Graveyard;
use App\Http\Requests\GraveyardRequest;

class GraveyardController extends Controller
{
    public function index()
    {
        $graveyards = Graveyard::with('zone')
            ->orderBy('zone_id')
            ->paginate(100)
            ->withQueryString();

        $zones = Zone::selectZones();

        return view('zones.graveyards.index', [
            'graveyards' => $graveyards,
            'zones' => $zones,
        ]);
    }

    public function store(GraveyardRequest $request)
    {
        $model = Graveyard::create($request->validated());

        toast()->success('Saved!', 'Graveyard created.');

        return response()->json([
            'success' => true,
            'data' => $model,
            'redirect' => url()->previous(),
        ], 201);
    }

    public function update(Graveyard $graveyard, GraveyardRequest $request)
    {
        $graveyard->update($request->validated());

        toast()->success('Saved!', 'Graveyard updated.');

        return response()->json([
            'success' => true,
            'data' => $graveyard,
            'redirect' => url()->previous(),
        ], 201);
    }

    public function destroy(Graveyard $graveyard)
    {
        $graveyard->delete();

        return back()->with('success', 'Graveyard deleted.');
    }
}
