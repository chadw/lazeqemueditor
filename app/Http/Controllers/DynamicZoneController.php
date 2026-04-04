<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use App\Models\DynamicZone;
use App\Filters\DynamicZoneFilter;
use Illuminate\Http\Request;
use App\Http\Requests\DynamicZoneRequest;

class DynamicZoneController extends Controller
{
    public function index(Request $request)
    {
        // get all dz's
        $query = DynamicZone::with([
            'leader' => function ($q) {
                $q->select('id', 'name');
            },
            'safe_zone' => function ($q) {
                $q->select('id', 'zoneidnumber', 'short_name');
            },
            'members.character' => function ($q) {
                $q->select('id', 'name');
            },
        ]);

        $query = (new DynamicZoneFilter($request))->apply($query);

        $dzs = $query
            ->withCount('members')
            ->sortable('name')
            ->paginate(100)
            ->withQueryString();

        // get zones for compass/safe
        $zones = Zone::selectZones();

        return view('dynamiczones.index', compact('dzs', 'zones'));
    }

    public function store(DynamicZoneRequest $request)
    {
        $data = $request->validated();

        $model = DynamicZone::create($data);
        toast()->success('Saved!', "Dynamic Zone created.");

        return response()->json([
            'success' => true,
            'data'    => $model->fresh(),
            'redirect'=> url()->previous(),
        ], 200);
    }

    public function update(DynamicZoneRequest $request, DynamicZone $dynamiczone)
    {
        $dynamiczone->update($request->validated());
        toast()->success('Saved!', "Dynamic Zone updated.");

        return response()->json([
            'success' => true,
            'data'    => $dynamiczone->fresh(),
            'redirect'=> url()->previous(),
        ], 200);
    }

    public function destroy(DynamicZone $dynamiczone)
    {
        $dynamiczone->delete();
        toast()->success('Deleted!', "Dynamic Zone deleted.");

        return back();
    }
}
