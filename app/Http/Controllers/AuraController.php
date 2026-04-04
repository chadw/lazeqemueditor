<?php

namespace App\Http\Controllers;

use App\Models\Aura;
use App\Models\Zone;
use App\Models\Spell;
use Illuminate\Http\Request;
use App\Http\Requests\AuraRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AuraController extends Controller
{
    protected $allSpells;
    protected $allZones;

    public function __construct()
    {
        $this->allSpells = collect(Cache::rememberForever('all_spells', function () {
            return Spell::pluck('name', 'id');
        }));

        $this->allZones = Cache::rememberForever('all_zones', function () {
            return Zone::select('id', 'short_name', 'long_name', 'expansion')
                ->orderBy('id')
                ->get()
                ->unique('short_name')
                ->keyBy('short_name');
        });

        view()->share('allSpells', $this->allSpells);
        view()->share('allZones', $this->allZones);
    }

    public function index(Request $request)
    {
        $auras = Aura::with(['spell', 'npc'])->orderBy('type')->get();

        return view('auras.index', compact('auras'));
    }

    public function store(AuraRequest $request)
    {
        $data = $request->validated();

        $model = Aura::create($data);

        toast()->success('Saved!', 'Aura created.');

        return response()->json([
            'success' => true,
            'data'    => $model,
            'redirect'=> route('auras.index'),
            'message' => 'Aura created.',
        ], 201);
    }

    public function update(AuraRequest $request, Aura $aura)
    {
        $data = $request->validated();
        $data['type'] = $aura->type;

        $aura->update($data);

        toast()->success('Saved!', 'Aura updated.');

        return response()->json([
            'success' => true,
            'data'    => $aura,
            'redirect'=> route('auras.index'),
            'message' => 'Aura updated.',
        ], 201);
    }

    public function destroy(Aura $aura)
    {
        $aura->delete();

        return back()->with('success', 'Aura deleted.');
    }
}
