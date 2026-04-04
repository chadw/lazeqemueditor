<?php

namespace App\Http\Controllers;

use App\Models\NpcSpellEntry;
use App\Http\Requests\NpcSpellEntryRequest;

class NpcSpellEntryController extends Controller
{
    public function store(NpcSpellEntryRequest $request)
    {
        $data = $request->validated();

        $model = NpcSpellEntry::create($data);

        toast()->success('Saved!', "NPC Spell entry added.");

        return response()->json([
            'success' => true,
            'data'    => $model,
            'redirect'=> url()->previous(),
        ], 200);
    }

    public function update(NpcSpellEntryRequest $request, NpcSpellEntry $npcSpellEntry)
    {
        $data = $request->validated();

        $npcSpellEntry->update($data);

        toast()->success('Saved!', "NPC Spell entry updated.");

        return response()->json([
            'success' => true,
            'data'    => $npcSpellEntry,
            'redirect'=> url()->previous(),
        ], 200);
    }

    public function destroy(NpcSpellEntry $npcSpellEntry)
    {
        $npcSpellEntry->delete();

        return back()->with('success', 'NPC Spell entry deleted.');
    }
}
