<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PetBeastlordDataRequest;
use App\Models\PetBeastlordData;

class BeastlordPetController extends Controller
{
    public function index(Request $request)
    {
        $beastlordPets = PetBeastlordData::get();

        return view('beastlord-pets.index', compact('beastlordPets'));
    }

    public function store(PetBeastlordDataRequest $request)
    {
        $model = PetBeastlordData::create($request->validated());

        toast()->success('Saved!', 'Beastlord Pet created.');

        return response()->json([
            'success' => true,
            'data'    => $model,
            'redirect'=> route('beastlord-pets.index'),
        ], 201);
    }

    public function update(PetBeastlordDataRequest $request, PetBeastlordData $pet)
    {
        $pet->update($request->validated());

        toast()->success('Saved!', 'Beastlord Pet updated.');

        return response()->json([
            'success' => true,
            'data'    => $pet,
            'redirect'=> route('beastlord-pets.index'),
        ], 201);
    }

    public function destroy(PetBeastlordData $pet)
    {
        $pet->delete();

        return back()->with('success', 'Beastlord Pet deleted.');
    }
}
