<?php

namespace App\Http\Controllers;

use App\Http\Requests\DoorRequest;
use App\Models\Door;
use App\Models\Zone;

class DoorController extends Controller
{
    public function store(Zone $zone, DoorRequest $request)
    {
        $data = $request->validated();
        $data['zone'] = $zone->short_name;

        $model = Door::create($data);
        toast()->success('Saved!', 'Door created.');

        return response()->json([
            'success' => true,
            'data'    => $model->fresh(),
            'redirect'=> url()->previous(),
        ], 201);
    }

    public function update(Zone $zone, Door $door, DoorRequest $request)
    {
        if ($door->zone !== $zone->short_name) {
            abort(404);
        }

        $data = $request->validated();
        $data['zone'] = $zone->short_name;

        $door->update($data);
        toast()->success('Saved!', 'Door updated.');

        return response()->json([
            'success' => true,
            'data'    => $door->fresh(),
            'redirect'=> url()->previous(),
        ], 201);
    }

    public function destroy(Zone $zone, Door $door)
    {
        if ($door->zone !== $zone->short_name) {
            abort(404);
        }

        $door->delete();
        toast()->success('Deleted!', 'Door deleted.');

        return back();
    }
}
