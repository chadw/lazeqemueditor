<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContainerObjectRequest;
use App\Models\ContainerObject;
use App\Models\Zone;

class ObjectController extends Controller
{
    public function store(Zone $zone, ContainerObjectRequest $request)
    {
        $data = $request->validated();
        $data['zoneid'] = $zone->zoneidnumber;

        $model = ContainerObject::create($data);
        toast()->success('Saved!', 'Object created.');

        return response()->json([
            'success'  => true,
            'data'     => $model->fresh(),
            'redirect' => url()->previous(),
        ], 201);
    }

    public function update(Zone $zone, ContainerObject $obj, ContainerObjectRequest $request)
    {
        if ($obj->zoneid !== $zone->zoneidnumber) {
            abort(404);
        }

        $data = $request->validated();
        $data['zoneid'] = $zone->zoneidnumber;

        $obj->update($data);
        toast()->success('Saved!', 'Object updated.');

        return response()->json([
            'success'  => true,
            'data'     => $obj->fresh(),
            'redirect' => url()->previous(),
        ], 200);
    }

    public function destroy(Zone $zone, ContainerObject $obj)
    {
        if ($obj->zoneid !== $zone->zoneidnumber) {
            abort(404);
        }

        $obj->delete();
        toast()->success('Deleted!', 'Object deleted.');

        return back();
    }
}
