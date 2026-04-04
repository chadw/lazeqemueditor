<?php

namespace App\Http\Controllers;

use App\Models\Fishing;
use App\Http\Requests\FishingRequest;
use App\Models\Zone;

class FishingController extends Controller
{
    public function store(Zone $zone, FishingRequest $request)
    {
        $data = $request->validated();
        $data['zoneid'] = $zone->zoneidnumber;

        $model = Fishing::create($data);
        toast()->success('Saved!', 'Fishing created.');

        return response()->json([
            'success'  => true,
            'data'     => $model->fresh(),
            'redirect' => url()->previous(),
        ], 201);
    }

    public function update(Zone $zone, Fishing $fish, FishingRequest $request)
    {
        if ($fish->zoneid !== $zone->zoneidnumber) {
            abort(404);
        }

        $data = $request->validated();
        $data['zoneid'] = $zone->zoneidnumber;

        $fish->update($data);
        toast()->success('Saved!', 'Fishing updated.');

        return response()->json([
            'success'  => true,
            'data'     => $fish->fresh(),
            'redirect' => url()->previous(),
        ], 200);
    }

    public function destroy(Zone $zone, Fishing $fish)
    {
        if ($fish->zoneid !== $zone->zoneidnumber) {
            abort(404);
        }

        $fish->delete();
        toast()->success('Deleted!', 'Fishing deleted.');

        return back();
    }
}
