<?php

namespace App\Http\Controllers;

use App\Http\Requests\SpawnGroupRequest;
use Illuminate\Http\Request;
use App\Models\SpawnGroup;

class SpawnGroupController extends Controller
{
    /**
     * Display an index of spawn groups.
     *
     * @param  mixed $request
     * @return void
     */
    public function index(Request $request)
    {
        $perPage = 25;

        $query = SpawnGroup::query()
            ->withCount(['spawnentries', 'spawn2'])
            ->orderBy('id', 'desc');

        $spawngroups = $query->paginate($perPage)->withQueryString();

        return view('spawngroups.index', compact('spawngroups'));
    }

    /**
     * Show the form for editing the specified spawn group.
     *
     * @param  mixed $spawngroup
     * @return void
     */
    public function edit(SpawnGroup $spawngroup)
    {
        $spawngroup->load([
            'spawnentries.npc',
            'spawn2.spawn2Disabled',
            'spawn2.zoneData',
            'spawn2.npcs',
        ]);

        return view('spawngroups.edit', compact('spawngroup'));
    }

    /**
     * save new spawn group
     *
     * @param  mixed $request
     * @return void
     */
    public function store(SpawnGroupRequest $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'id' => 'nullable|integer',
        ]);

        $sg = new SpawnGroup();
        if (isset($data['id'])) $sg->id = (int)$data['id'];
        $sg->name = $data['name'];
        $sg->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'id' => $sg->id,
                'data' => $sg,
            ], 201);
        }

        return back()->with('success', 'Spawn Group created.');
    }

    /**
     * update existing spawn group
     *
     * @param  mixed $spawngroup
     * @param  mixed $request
     * @return void
     */
    public function update(SpawnGroup $spawngroup, SpawnGroupRequest $request)
    {
        $data = $request->validated();

        $spawngroup->update($data);

        toast()->success('Saved!', 'Spawn Group updated.');

        return back();
    }

    /**
     * delete spawn group, entries, spawn2 and spawn2 disabled
     *
     * @param  mixed $spawngroup
     * @return void
     */
    public function destroy(SpawnGroup $spawngroup)
    {
        //$spawngroup->delete();
        toast()->error('Oops', 'This is not done yet.');

        /*
        $query = "DELETE FROM spawngroup WHERE id=$sid";
        $query = "DELETE FROM spawnentry WHERE spawngroupID=$sid";
        $query = "DELETE FROM spawn2_disabled WHERE spawn2_id IN (SELECT id FROM spawn2 WHERE spawngroupID=$sid)";
        $query = "DELETE FROM spawn2 WHERE spawngroupID=$sid";
        */

        return back();
    }
}
