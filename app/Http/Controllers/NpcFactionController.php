<?php

namespace App\Http\Controllers;

use App\Http\Requests\NpcFactionRequest;
use App\Models\NpcFaction;
use App\Models\NpcType;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class NpcFactionController extends Controller
{
    /**
     * create new npc faction and assign to npc.
     *
     * @param  mixed $request
     * @return void
     */
    public function store(NpcFactionRequest $request)
    {
        $data = $request->validated();
        $faction = NpcFaction::create($data);

        $assigned = false;
        if (!empty($data['npc_id'])) {
            $assigned = (bool) NpcType::where('id', $data['npc_id'])->update([
                'npc_faction_id' => $faction->id
            ]);
        }

        $message = "NPC Faction <b>[%s]</b> created" . ($assigned ? " and assigned to NPC." : ".");
        toast()->success('Saved!', $message, [$faction->name]);

        return back();
    }

    /**
     * search using ajaxSelect
     *
     * @param  mixed $request
     * @return void
     */
    public function search(Request $request)
    {
        $search = $request->string('q');

        return NpcFaction::query()
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
     * options for hydrator
     *
     * @return Collection
     */
    public function options(): Collection
    {
        return NpcFaction::options();
    }
}
