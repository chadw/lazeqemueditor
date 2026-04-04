<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForageRequest;
use App\Models\Forage;
use App\Models\Zone;

class ForageController extends Controller
{
    public function store(Zone $zone, ForageRequest $request)
    {
        $data = $request->validated();
        $data['zoneid'] = $zone->zoneidnumber;

        $model = Forage::create($data);
        toast()->success('Saved!', 'Forage created.');

        return response()->json([
            'success' => true,
            'data'    => $model->fresh(),
            'redirect'=> url()->previous(),
        ], 201);
    }

    public function update(Zone $zone, Forage $forage, ForageRequest $request)
    {
        if ($forage->zoneid !== $zone->zoneidnumber) {
            abort(404);
        }

        $data = $request->validated();
        $data['zoneid'] = $zone->zoneidnumber;

        $forage->update($data);
        toast()->success('Saved!', 'Forage updated.');

        return response()->json([
            'success' => true,
            'data'    => $forage->fresh(),
            'redirect'=> url()->previous(),
        ], 201);
    }

    public function destroy(Zone $zone, Forage $forage)
    {
        if ($forage->zoneid !== $zone->zoneidnumber) {
            abort(404);
        }

        $forage->delete();
        toast()->success('Deleted!', 'Forage deleted.');

        return back();
    }
}
