<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\PetEquipmentsetRequest;
use App\Models\PetEquipmentset;
use App\Models\PetEquipmentsetEntry;

class PetEquipmentController extends Controller
{
    public function index(Request $request)
    {
        $petEquip = PetEquipmentset::with('petEquipmentsetEntries.item')
            ->paginate(100)
            ->withQueryString();

        return view('pets.equipment.index', compact('petEquip'));
    }

    public function store(PetEquipmentsetRequest $request)
    {
        $data = $request->validated();

        if (empty($data['set_id'])) {
            $data['set_id'] = (int) PetEquipmentset::max('set_id') + 1;
        }

        $model = PetEquipmentset::create($data);

        toast()->success('Saved!', "Pet Equipment Set created.");

        return response()->json([
            'success'  => true,
            'data'     => $model,
            'redirect' => url()->previous(),
        ], 200);
    }

    public function update(PetEquipmentsetRequest $request, PetEquipmentset $equipment)
    {
        $data = $request->validated();

        $equipment->update($data);

        toast()->success('Saved!', "Pet Equipment Set updated.");

        return response()->json([
            'success'  => true,
            'data'     => $equipment,
            'redirect' => url()->previous(),
        ], 200);
    }

    public function destroy(PetEquipmentset $equipment)
    {
        DB::transaction(function () use ($equipment) {
            PetEquipmentsetEntry::where('set_id', $equipment->set_id)->delete();
            $equipment->delete();
        });

        return back()->with('success', 'Pet Equipment set and entries deleted.');
    }
}
