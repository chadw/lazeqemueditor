<?php

namespace App\Http\Controllers;

use App\Http\Requests\BlockedSpellRequest;
use App\Models\BlockedSpell;
use App\Models\Zone;

class BlockedSpellController extends Controller
{
    public function store(Zone $zone, BlockedSpellRequest $request)
    {
        $data = $request->validated();
        $data['zoneid'] = $zone->zoneidnumber;

        $model = BlockedSpell::create($data);
        toast()->success('Saved!', 'Blocked Spell created.');

        return response()->json([
            'success'  => true,
            'data'     => $model->fresh(),
            'redirect' => url()->previous(),
        ], 201);
    }

    public function update(Zone $zone, BlockedSpell $blockedSpell, BlockedSpellRequest $request)
    {
        if ($blockedSpell->zoneid !== $zone->zoneidnumber) {
            abort(404);
        }

        $data = $request->validated();
        $data['zoneid'] = $zone->zoneidnumber;

        $blockedSpell->update($data);
        toast()->success('Saved!', 'Blocked Spell updated.');

        return response()->json([
            'success'  => true,
            'data'     => $blockedSpell->fresh(),
            'redirect' => url()->previous(),
        ], 201);
    }

    public function destroy(Zone $zone, BlockedSpell $blockedSpell)
    {
        if ($blockedSpell->zoneid !== $zone->zoneidnumber) {
            abort(404);
        }

        $blockedSpell->delete();
        toast()->success('Deleted!', 'Blocked Spell deleted.');

        return back();
    }
}
