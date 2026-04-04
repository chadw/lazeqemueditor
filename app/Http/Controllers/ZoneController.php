<?php

namespace App\Http\Controllers;

use App\Http\Requests\ZoneRequest;
use App\Models\Fishing;
use App\Models\Forage;
use App\Models\Graveyard;
use App\Models\Zone;
use App\Support\ObjectSprite;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ZoneController extends Controller
{
    public function index(Request $request)
    {
        $zoneId  = (int) $request->query('zone');
        $version = $request->query('v');

        $allZones = Zone::getAllZones();

        $zones = Zone::baseZones();

        return view('zones.index', [
            'allZones' => $allZones,
            'zones' => $zones,
            'selectedZoneId'  => $zoneId,
            'selectedVersion' => $version ?? null,
            'versions'        => Zone::versionsFor($zoneId),
        ]);
    }

    public function edit(Request $request, Zone $zone)
    {
        $zones = Zone::baseZones();
        $versionToLoad = (int) $zone->version;

        $zone->load([
            'zonepoints' => fn ($q) =>
                $q->where('version', $versionToLoad)
                  ->with('targetZones'),

            'doors' => fn ($q) =>
                $q->whereIn('version', [-1, $versionToLoad])
                  ->with('key')
                  ->orderBy('doorid'),

            'groundspawns' => fn ($q) =>
                $q->where('version', $versionToLoad)
                  ->with('item_'),

            'traps' => fn ($q) =>
                $q->where('version', $versionToLoad)
                  ->with(['spell', 'npc']),

            'objects' => fn ($q) =>
                $q->where('version', $versionToLoad)
                  ->with('item'),

            'blockedSpells.spell',
            //'custobjdata' // disabled for now and just use text field
        ]);

        $fishing = Fishing::with('item')
            ->forZone($zone->zoneidnumber)
            ->get();

        $forages = Forage::with('item')
            ->forZone($zone->zoneidnumber)
            ->get();

        // objects for modal
        $objectIds = ObjectSprite::ids();

        $graveyards = Graveyard::with('zone')->get()
            ->mapWithKeys(fn($g) => [ $g->id => $g->zone?->short_name ?? 'Unknown' ])
            ->toArray();

        return view('zones.edit', [
            'zones'           => $zones,
            'zone'            => $zone,
            'versions'        => Zone::versionsFor($zone->zoneidnumber),
            'selectedZoneId'  => $zone->zoneidnumber,
            'selectedVersion' => $zone->id,
            'graveyards'      => $graveyards,
            'fishing'         => $fishing,
            'forages'         => $forages,
            'objectIds'       => $objectIds
        ]);
    }

    public function update(ZoneRequest $request, Zone $zone)
    {
        $data = $request->validated();
        $data['zoneidnumber'] = $zone->zoneidnumber;
        $data['short_name'] = $zone->short_name;

        $zone->update($data);

        return back();
    }

    public function options(): Collection
    {
        return Zone::zoneOptions();
    }
}
