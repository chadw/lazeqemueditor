<?php

namespace App\Http\Controllers;

use App\Http\Requests\SpawnEntryRequest;
use App\Models\SpawnEntry;
use App\Models\SpawnGroup;
use Illuminate\Http\Request;

class SpawnEntryController extends Controller
{
    public function store(SpawnEntryRequest $request, SpawnGroup $spawngroup)
    {
        $data = $request->validated();
        $data['spawngroupID'] = $spawngroup->id;
        $data['npcID'] = (int)($data['npcID'] ?? 0);

        $model = SpawnEntry::create($data);
        toast()->success('Saved!', 'Spawn entry created.');

        return response()->json([
            'success' => true,
            'data'    => $model->fresh(),
            'redirect'=> url()->previous(),
        ], 201);
    }

    public function update(SpawnEntryRequest $request, SpawnGroup $spawngroup, $npcID)
    {
        $data = $request->validated();

        $entry = SpawnEntry::where('spawngroupID', $spawngroup->id)
            ->where('npcID', $npcID)
            ->firstOrFail();

        if (($request->input('npcID') ?? null) != $npcID) {
            $newNpcId = (int)$request->input('npcID');

            $entry->delete();

            $model = SpawnEntry::create([
                'spawngroupID' => $spawngroup->id,
                'npcID' => $newNpcId,
                'chance' => $data['chance'] ?? 100,
                'condition_value_filter' => $data['condition_value_filter'] ?? 1,
                'min_time' => $data['min_time'] ?? 0,
                'max_time' => $data['max_time'] ?? 0,
                'min_expansion' => $data['min_expansion'] ?? -1,
                'max_expansion' => $data['max_expansion'] ?? -1,
                'content_flags' => $data['content_flags'] ?? '',
                'content_flags_disabled' => $data['content_flags_disabled'] ?? '',
            ]);

            toast()->success('Saved!', 'Spawn entry updated.');

            return response()->json([
                'success' => true,
                'data'    => $model->fresh(),
                'redirect'=> url()->previous(),
            ], 201);
        }

        unset($data['spawngroupID'], $data['npcID']);

        $entry->update($data);

        toast()->success('Saved!', 'Spawn entry updated.');

        return response()->json([
            'success' => true,
            'data'    => $entry->fresh(),
            'redirect'=> url()->previous(),
        ], 201);
    }

    public function destroy(SpawnEntry $entry)
    {
        $entry->delete();

        toast()->success('Deleted', 'Spawn entry removed.');

        return back();
    }
}
