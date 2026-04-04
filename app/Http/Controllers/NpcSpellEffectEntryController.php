<?php

namespace App\Http\Controllers;

use App\Models\NpcSpellEffectEntry;
use App\Http\Requests\NpcSpellEffectEntryRequest;

class NpcSpellEffectEntryController extends Controller
{
    public function store(NpcSpellEffectEntryRequest $request)
    {
        $data = $request->validated();

        NpcSpellEffectEntry::create($data);

        return back()->with('success', 'NPC Spell Effect entry added.');
    }

    public function update(NpcSpellEffectEntryRequest $request, NpcSpellEffectEntry $npcSpellEffectEntry)
    {
        $data = $request->validated();

        $npcSpellEffectEntry->update($data);

        return back()->with('success', 'NPC Spell Effect entry updated.');
    }

    public function destroy(NpcSpellEffectEntry $npcSpellEffectEntry)
    {
        $npcSpellEffectEntry->delete();

        return back()->with('success', 'NPC Spell Effect entry deleted.');
    }
}
