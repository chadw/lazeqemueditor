<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PetEquipmentsetEntryRequest;
use App\Models\PetEquipmentsetEntry;

class PetEquipmentEntryController extends Controller
{
    public function store(PetEquipmentsetEntryRequest $request)
    {
        $data = $request->validated();

        $model = PetEquipmentsetEntry::create($data);
        toast()->success('Saved!', 'Pet equipment entry created.');

        return response()->json([
            'success'  => true,
            'data'     => $model,
            'redirect' => url()->previous(),
            'message'  => 'Pet equipment entry created.',
        ], 201);
    }

    public function update(PetEquipmentsetEntryRequest $request, int $set, int $slot)
    {
        $row = PetEquipmentsetEntry::where(['set_id' => $set, 'slot' => $slot])
            ->firstOrFail();

        $data = $request->validated();

        $row->update($data);

        toast()->success('Saved!', 'Pet equipment entry updated.');

        return response()->json([
            'success'  => true,
            'data'     => $row,
            'redirect' => url()->previous(),
            'message'  => 'Pet equipment entry updated.',
        ], 201);
    }

    public function destroy($set, $slot)
    {
        $equip = PetEquipmentsetEntry::where('set_id', (int) $set)
            ->where('slot', (int) $slot)
            ->firstOrFail();

        $equip->delete();

        toast()->success('Saved!', 'Pet equipment entry deleted.');

        return back();
    }
}
