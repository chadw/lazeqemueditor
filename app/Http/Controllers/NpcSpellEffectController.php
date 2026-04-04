<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NpcSpellEffect;
use App\Http\Requests\NpcSpellEffectRequest;

class NpcSpellEffectController extends Controller
{
    public function index(Request $request)
    {
        $npc_spell_effects = NpcSpellEffect::with(['npcSpellEffectEntries'])->get();

        return view('npc-spell-effects.index', compact('npc_spell_effects'));
    }

    public function edit(NpcSpellEffect $npcSpellEffect)
    {
        $npcSpellEffect = NpcSpellEffect::with(['npcSpellEffectEntries'])
            ->where('id', $npcSpellEffect->id)
            ->firstOrFail();

        return view('npc-spell-effects.edit', compact('npcSpellEffect'));
    }

    public function store(NpcSpellEffectRequest $request)
    {
        $data = $request->validated();

        NpcSpellEffect::create($data);

        return back()->with('success', 'NPC Spell Effect created.');
    }

    public function update(NpcSpellEffectRequest $request, NpcSpellEffect $npcSpellEffect)
    {
        $data = $request->validated();

        $npcSpellEffect->update($data);
        toast()->success('Saved!', 'NPC Spell Effect updated.');

        return back();
    }

    public function destroy(NpcSpellEffect $npcSpellEffect)
    {
        $npcSpellEffect->delete();
        toast()->success('Deleted!', 'NPC Spell Effect deleted.');

        return back();
    }
}
