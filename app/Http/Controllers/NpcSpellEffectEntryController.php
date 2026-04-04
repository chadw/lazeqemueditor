<?php

namespace App\Http\Controllers;

use App\Models\NpcSpellEffectEntry;
use App\Http\Requests\NpcSpellEffectEntryRequest;

class NpcSpellEffectEntryController extends Controller
{
    public function store(NpcSpellEffectEntryRequest $request)
    {
        $data = $request->validated();

        $model = NpcSpellEffectEntry::create($data);
        toast()->success('Saved!', 'NPC Spell Effect entry added.');

        return response()->json([
            'success'  => true,
            'data'     => $model->fresh(),
            'redirect' => url()->previous(),
        ], 201);
    }

    public function update(NpcSpellEffectEntryRequest $request, NpcSpellEffectEntry $npcSpellEffectEntry)
    {
        $data = $request->validated();

        $npcSpellEffectEntry->update($data);
        toast()->success('Saved!', 'NPC Spell Effect entry updated.');

        return response()->json([
            'success'  => true,
            'data'     => $npcSpellEffectEntry->fresh(),
            'redirect' => url()->previous(),
        ], 201);
    }

    public function destroy(NpcSpellEffectEntry $npcSpellEffectEntry)
    {
        $npcSpellEffectEntry->delete();
        toast()->success('Deleted!', 'NPC Spell Effect entry deleted.');

        return back();
    }
}
