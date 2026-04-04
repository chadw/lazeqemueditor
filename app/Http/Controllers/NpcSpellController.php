<?php

namespace App\Http\Controllers;

use App\Http\Requests\NpcSpellRequest;
use App\Models\NpcSpell;
use App\Models\NpcType;
use App\Models\NpcSpellEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class NpcSpellController extends Controller
{
    public function index(Request $request)
    {
        $npcSpells = NpcSpell::with([
            'attackProcSpell',
            'parentSet',
        ])
            ->withCount('npcSpellEntries')
            ->orderBy('id')
            ->paginate(100)
            ->withQueryString();

        $pageIds = $npcSpells->pluck('id')->filter()->values()->all();
        $usageCounts = [];
        if (!empty($pageIds)) {
            $usageCounts = NpcType::selectRaw('npc_spells_id, count(*) as total')
                ->whereIn('npc_spells_id', $pageIds)
                ->groupBy('npc_spells_id')
                ->pluck('total', 'npc_spells_id')
                ->toArray();
        }


        // parent select
        $allNpcSpells = NpcSpell::orderBy('id')->pluck('name', 'id')->toArray();

        return view('npc-spells.index', compact('npcSpells', 'allNpcSpells', 'usageCounts'));
    }

    public function edit(NpcSpell $npcSpell)
    {
        $npcSpell = NpcSpell::with([
            'attackProcSpell',
            'rangeProcSpell',
            'defensiveProcSpell',
            'npcSpellEntries.spells',
            'parentSet.npcSpellEntries.spells',
        ])
            ->where('id', $npcSpell->id)
            ->firstOrFail();

        // parent select
        $allNpcSpells = NpcSpell::orderBy('id')->pluck('name', 'id')->toArray();

        return view('npc-spells.edit', compact('npcSpell', 'allNpcSpells'));
    }

    public function store(NpcSpellRequest $request)
    {
        $data = $request->validated();
        $npcSpell = NpcSpell::create($data);

        $assigned = false;
        if (!empty($data['npc_id'])) {
            $assigned = (bool) NpcType::where('id', $data['npc_id'])->update([
                'npc_spells_id' => $npcSpell->id
            ]);
        }

        $message = "NPC Spell Set <b>[%s]</b> created" . ($assigned ? " and assigned to NPC." : ".");
        toast()->success('Saved!', $message, [$npcSpell->name]);

        return response()->json([
            'success' => true,
            'data'    => $npcSpell,
            'redirect'=> url()->previous(),
        ], 200);
    }

    public function update(NpcSpellRequest $request, NpcSpell $npcSpell)
    {
        $data = $request->validated();

        $npcSpell->update($data);

        toast()->success('Saved!', "NPC Spell Set updated.");

        return response()->json([
            'success' => true,
            'data'    => $npcSpell,
            'redirect'=> url()->previous(),
        ], 200);
    }

    public function destroy(NpcSpell $npcSpell)
    {
        $npcSpell->delete();

        return back()->with('success', 'NPC Spell Set deleted.');
    }

    /**
     * used for ajaxSelect
     *
     * @param  mixed $request
     * @return void
     */
    public function search(Request $request)
    {
        $search = $request->string('q');

        return NpcSpell::query()
            ->select('id', 'name')
            ->when($search, function ($q) use ($search) {
                $q->where('id', $search)
                    ->orWhere('name', 'like', "%{$search}%");
            })
            ->orderBy('id')
            ->limit(50)
            ->get();
    }

    /**
     * options for select hydrator
     *
     * @return Collection
     */
    public function options(): Collection
    {
        // parent select
        return NpcSpell::npcSpellOptions();
    }
}
