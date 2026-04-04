<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FactionValue;
use App\Http\Requests\FactionValueRequest;
use App\Filters\FactionValueFilter;

class FactionValueController extends Controller
{
    public function index(Request $request)
    {
        $query = FactionValue::with(['character', 'faction']);

        $factions = (new FactionValueFilter($request))
            ->apply($query)
            //->sortable('char_id', 'asc')
            ->paginate(50)
            ->withQueryString();

        return view('factions.characters.index', compact('factions'));
    }

    public function store(FactionValueRequest $request, $char_id = null)
    {
        $data = $request->validated();

        if ($char_id) {
            $data['char_id'] = $char_id;
        }

        $charId = $data['char_id'] ?? null;

        if (!$charId) {
            return response()->json([
                'success' => false,
                'message' => 'Character id is required.',
            ], 422);
        }

        $factionId = $data['faction_id'] ?? null;
        if (!$factionId) {
            return response()->json([
                'success' => false,
                'message' => 'Faction id is required.',
            ], 422);
        }

        $exists = FactionValue::where('char_id', $charId)
            ->where('faction_id', $factionId)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Character already has this faction.',
            ], 409);
        }

        $model = FactionValue::create($data);

        toast()->success('Saved!', 'Faction Value created.');

        return response()->json([
            'success' => true,
            'data'    => $model,
            'redirect'=> url()->previous(),
        ], 201);
    }

    public function update(FactionValueRequest $request, $char_id, $faction_id)
    {
        $factionValue = FactionValue::where('char_id', $char_id)
            ->where('faction_id', $faction_id)
            ->firstOrFail();

        $factionValue->update($request->only(['current_value', 'temp']));

        toast()->success('Saved!', 'Faction Value updated.');

        return response()->json([
            'success' => true,
            'data'    => $factionValue,
            'redirect'=> url()->previous(),
        ], 201);
    }

    public function destroy($char_id, $faction_id)
    {
        $factionValue = FactionValue::where('char_id', $char_id)
            ->where('faction_id', $faction_id)
            ->first();

        if ($factionValue) {
            $factionValue->delete();
        }

        toast()->success('Saved!', 'Faction Value removed.');

        return back()->with('success', 'Faction Value removed.');
    }
}
