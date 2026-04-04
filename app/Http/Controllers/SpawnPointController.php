<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SpawnTwoRequest;
use App\Models\SpawnTwo;
use App\Models\SpawnGroup;
use App\Models\SpawnTwoDisabled;

class SpawnPointController extends Controller
{
    public function store(SpawnTwoRequest $request, SpawnGroup $spawngroup)
    {
        $data = $request->validated();

        $model = SpawnTwo::create($data);

        $instanceId = isset($data['instance_id']) ? (int)$data['instance_id'] : 0;
        if ($request->boolean('disabled')) {
            SpawnTwoDisabled::updateOrCreate(
                ['spawn2_id' => $model->id, 'instance_id' => $instanceId],
                ['disabled' => 1]
            );
        }

        toast()->success('Saved!', 'Spawnpoint created.');

        return response()->json([
            'success' => true,
            'data'    => $model->fresh(),
            'redirect'=> url()->previous(),
        ], 201);
    }

    public function update(SpawnTwoRequest $request, SpawnGroup $spawngroup, SpawnTwo $spawnpoint)
    {
        $data = $request->validated();

        $spawnpoint->update($data);

        $instanceId = isset($data['instance_id']) ? (int)$data['instance_id'] : 0;
        if ($request->boolean('disabled')) {
            SpawnTwoDisabled::updateOrCreate(
                ['spawn2_id' => $spawnpoint->id, 'instance_id' => $instanceId],
                ['disabled' => 1]
            );
        } else {
            SpawnTwoDisabled::where('spawn2_id', $spawnpoint->id)
                ->when($instanceId !== null, function ($q) use ($instanceId) {
                    return $q->where('instance_id', $instanceId);
                })->delete();
        }

        toast()->success('Saved!', 'Spawnpoint updated.');

        return response()->json([
            'success' => true,
            'data'    => $spawnpoint->fresh(),
            'redirect'=> url()->previous(),
        ], 201);
    }

    public function destroy(SpawnGroup $spawngroup, SpawnTwo $spawnpoint)
    {
        SpawnTwoDisabled::where('spawn2_id', $spawnpoint->id)->delete();

        $spawnpoint->delete();
        toast()->success('Deleted!', 'Spawnpoint deleted.');

        return back();
    }
}
