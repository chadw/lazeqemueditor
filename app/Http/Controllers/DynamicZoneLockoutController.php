<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DynamicZoneLockout;
use App\Http\Requests\DynamicZoneLockoutRequest;
use App\Filters\DynamicZoneLockoutFilter;

class DynamicZoneLockoutController extends Controller
{
    public function index(Request $request)
    {
        $query = DynamicZoneLockout::with([
            'dz' => fn($q) => $q->select('id', 'name')
                ->withCount('members')
                ->with(['members' => fn($m) => $m->with([
                    'character' => fn($c) => $c->select('id', 'name')
                ])
                ->select('id', 'dynamic_zone_id', 'character_id')
            ])
        ]);

        $query = (new DynamicZoneLockoutFilter($request))->apply($query);

        $lockouts = $query
            ->sortable('expire_time', 'desc')
            ->paginate(50)
            ->withQueryString();

        return view('dynamiczones.lockouts.index', compact('lockouts'));
    }

    public function store(DynamicZoneLockoutRequest $request)
    {
        $data = $request->validated();

        if (array_key_exists('expire_time', $data) && $data['expire_time'] === '') {
            unset($data['expire_time']);
        }

        $model = DynamicZoneLockout::create($data);
        toast()->success('Saved!', "DZ Lockout created.");

        return response()->json([
            'success' => true,
            'data'    => $model->fresh(),
            'redirect' => url()->previous(),
        ], 200);
    }

    public function update(DynamicZoneLockoutRequest $request, DynamicZoneLockout $lockout)
    {
        $data = $request->validated();
        $data['dynamic_zone_id'] = $lockout->dynamic_zone_id;
        $data['from_expedition_uuid'] = $lockout->from_expedition_uuid;

        if (array_key_exists('expire_time', $data) && $data['expire_time'] === '') {
            unset($data['expire_time']);
        }

        $lockout->update($data);
        toast()->success('Saved!', "DZ Lockout updated.");

        return response()->json([
            'success' => true,
            'data'    => $lockout->fresh(),
            'redirect' => url()->previous(),
        ], 200);
    }

    public function destroy(DynamicZoneLockout $lockout)
    {
        $lockout->delete();
        toast()->success('Deleted!', "DZ Lockout deleted.");

        return back();
    }
}
