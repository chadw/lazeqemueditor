<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\Spell;
use Illuminate\Http\Request;
use App\Http\Requests\PetRequest;
use App\Models\PetEquipmentset;
use Illuminate\Support\Facades\Cache;

class PetController extends Controller
{
    protected $allSpells;

    public function __construct()
    {
        $this->allSpells = collect(Cache::rememberForever('pet_spells', function () {
            return Spell::select([
                'id',
                'name',
                'new_icon',
                'teleport_zone',
                ...collect(range(1, 16))->map(fn($i) => "classes{$i}")->toArray(),
            ])
                ->hasAnyEffect([33, 67, 71, 106, 108, 152, 167])
                ->groupBy('teleport_zone')
                ->get();
        }));

        view()->share('allSpells', $this->allSpells);
    }

    public function index(Request $request)
    {
        $pets = Pet::with([
            'npc:id,name,level,race,class,bodytype,hp,AC,mindmg,maxdmg',
            'equipment.petEquipmentsetEntries.item',
        ])
            ->sortable('type', 'asc')
            //->orderBy('id', 'asc')
            ->paginate(100)
            ->withQueryString();

        $petEquip = PetEquipmentset::pluck('setname', 'set_id')->toArray();

        return view('pets.index', compact('pets', 'petEquip'));
    }

    public function store(PetRequest $request)
    {
        $data = $request->validated();

        Pet::create($data);

        return back()->with('success', 'Pet created.');
    }

    public function update(PetRequest $request, Pet $pet)
    {
        $data = $request->validated();

        $pet->update($data);

        return back()->with('success', 'Pet updated.');
    }

    public function destroy(Pet $pet)
    {
        $pet->delete();

        return back()->with('success', 'Pet deleted.');
    }

    public function popup($pet)
    {
        $pet = Pet::where('id', $pet)
            ->orWhere('type', $pet)
            ->with('npc')
            ->firstOrFail();

        return response()->json([
            'html' => view('pets.partials.popup', ['pet' => $pet])->render()
        ]);
    }
}
