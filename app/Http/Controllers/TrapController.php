<?php

namespace App\Http\Controllers;

use App\Http\Requests\TrapRequest;
use App\Models\Trap;
use App\Models\Zone;

class TrapController extends Controller
{
    public function store(Zone $zone, TrapRequest $request)
    {
        $data = $request->validated();
        $data['zone'] = $zone->short_name;

        $model = Trap::create($data);
        toast()->success('Saved!', 'Trap created.');

        return response()->json([
            'success'  => true,
            'data'     => $model->fresh(),
            'redirect' => url()->previous(),
        ], 201);
    }

    public function update(Zone $zone, Trap $trap, TrapRequest $request)
    {
        if ($trap->zone !== $zone->short_name) {
            abort(404);
        }

        $data = $request->validated();
        $data['zone'] = $zone->short_name;

        $trap->update($data);
        toast()->success('Saved!', 'Trap updated.');

        return response()->json([
            'success'  => true,
            'data'     => $trap->fresh(),
            'redirect' => url()->previous(),
        ], 200);
    }

    public function destroy(Zone $zone, Trap $trap)
    {
        if ($trap->zone !== $zone->short_name) {
            abort(404);
        }

        $trap->delete();
        toast()->success('Deleted!', 'Trap deleted.');

        return back();
    }
}
