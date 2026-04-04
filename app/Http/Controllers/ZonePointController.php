<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use App\Models\ZonePoint;
use App\Http\Requests\ZonePointRequest;

class ZonePointController extends Controller
{
    public function store(Zone $zone, ZonePointRequest $request)
    {
        $data = $request->validated();
        $data['zone'] = $zone->short_name;

        $model = ZonePoint::create($data);
        toast()->success('Saved!', 'Zone point created.');

        return response()->json([
            'success'  => true,
            'data'     => $model->fresh(),
            'redirect' => url()->previous(),
        ], 201);
    }

    public function update(Zone $zone, ZonePoint $zonePoint, ZonePointRequest $request)
    {
        if ($zonePoint->zone !== $zone->short_name) {
            abort(404);
        }

        $data = $request->validated();
        $data['zone'] = $zone->short_name;

        $zonePoint->update($data);
        toast()->success('Saved!', 'Zone point updated.');

        return response()->json([
            'success'  => true,
            'data'     => $zonePoint->fresh(),
            'redirect' => url()->previous(),
        ], 200);
    }

    public function destroy(Zone $zone, ZonePoint $zonePoint)
    {
        if ($zonePoint->zone !== $zone->short_name) {
            abort(404);
        }

        $zonePoint->delete();
        toast()->success('Deleted!', 'Zone point deleted.');

        return back();
    }
}
