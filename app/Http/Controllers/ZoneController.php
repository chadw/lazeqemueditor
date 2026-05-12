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
use App\Services\ZoneCloner;

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

    public function clone(Request $request, Zone $zone, ZoneCloner $cloner)
    {
        if ($request->input('export') === 'sql') {
            $sql = $cloner->generateSql($zone);

            $short = $zone->short_name;
            $filename = "zone-clone-{$short}-v" . (Zone::where('zoneidnumber', $zone->zoneidnumber)->max('version') + 1) . ".sql";

            return response()->streamDownload(function () use ($sql) {
                echo $sql;
            }, $filename, [
                'Content-Type' => 'application/sql',
            ]);
        }

        $newZone = $cloner->cloneZone($zone);

        return redirect()->route('zones.edit', ['zone' => $newZone->id]);
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

    public function search(Request $request)
    {
        $q = (string) $request->query('q', '');

        $query = Zone::query()
            ->select('zoneidnumber as id', 'short_name as name', 'long_name')
            ->groupBy('zoneidnumber', 'short_name', 'long_name')
            ->orderBy('short_name');

        if ($q !== '') {
            $like = '%' . $q . '%';
            if (is_numeric($q)) {
                $query->where('zoneidnumber', (int) $q);
            }
            $query->orWhere('short_name', 'like', $like)
                  ->orWhere('long_name', 'like', $like);
        }

            $results = $query->limit(200)->get();

            return response()->json($results->map(function ($r) {
                $short = $r->name ?? ($r->short_name ?? '');
                $long = $r->long_name ?? '';
                return [
                    'id' => $r->id,
                    'name' => trim($short . ' - ' . $long),
                ];
            })->values());
    }
}
