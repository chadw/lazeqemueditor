<?php

namespace App\Http\Controllers;

use App\Http\Requests\GroundSpawnRequest;
use App\Models\GroundSpawn;
use App\Models\Zone;

class GroundSpawnController extends Controller
{
    public function store(Zone $zone, GroundSpawnRequest $request)
    {
        $data = $request->validated();
        $data['zoneid'] = $zone->zoneidnumber;

        $model = GroundSpawn::create($data);
        toast()->success('Saved!', 'Ground Spawn created.');

        return response()->json([
            'success'  => true,
            'data'     => $model->fresh(),
            'redirect' => url()->previous(),
        ], 201);
    }

    public function update(Zone $zone, GroundSpawn $groundspawn, GroundSpawnRequest $request)
    {
        if ($groundspawn->zoneid !== $zone->zoneidnumber) {
            abort(404);
        }

        $data = $request->validated();
        $data['zoneid'] = $zone->zoneidnumber;

        $groundspawn->update($data);
        toast()->success('Saved!', 'Ground Spawn updated.');

        return response()->json([
            'success'  => true,
            'data'     => $groundspawn->fresh(),
            'redirect' => url()->previous(),
        ], 200);
    }

    public function destroy(Zone $zone, GroundSpawn $groundspawn)
    {
        if ($groundspawn->zoneid !== $zone->zoneidnumber) {
            abort(404);
        }

        $groundspawn->delete();
        toast()->success('Deleted!', 'Ground Spawn deleted.');

        return back();
    }
}
