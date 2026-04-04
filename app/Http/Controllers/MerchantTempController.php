<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use App\Models\NpcType;
use Illuminate\Http\Request;
use App\Models\MerchantlistTemp;
use App\Http\Requests\MerchantlistTempRequest;

class MerchantTempController extends Controller
{
    public function index(Request $request, $merchant)
    {
        $selectedZoneId = $request->query('zone');
        $selectedVersion = $request->query('v', 0);
        $selectedNpcId = $merchant;

        $tempItems = MerchantListTemp::with([
            'items',
            'npc' => fn($q) =>
                $q->select('id', 'name', 'merchant_id'),
            'zone' => fn($q) =>
                $q->select('zoneidnumber', 'short_name', 'long_name')
        ])
        ->where('npcid', $merchant)
        ->orderBy('slot')
        ->paginate(50)
        ->withQueryString();

        $zones = Zone::baseZones();
        $versions = [];
        $npcs = collect();
        $selectedNpc = $tempItems->first()->npc ?? NpcType::find($merchant);

        if ($selectedZoneId) {
            $zone = Zone::where('zoneidnumber', $selectedZoneId)->firstOrFail();

            $versions = Zone::where('zoneidnumber', $selectedZoneId)
                ->orderBy('version')
                ->get(['version']);

            $npcs = NpcType::getForZone($zone, (int) $selectedVersion, true);
        }

        return view('merchants.temp.index', compact(
            'tempItems',
            'selectedNpc',
            'zones',
            'versions',
            'npcs',
            'selectedZoneId',
            'selectedVersion',
            'selectedNpcId'
        ));
    }

    public function destroy(Request $request, $merchant, $slot, $zone_id, $instance_id)
    {
        $entry = MerchantlistTemp::where('npcid', $merchant)
            ->where('slot', $slot)
            ->where('zone_id', $zone_id)
            ->where('instance_id', $instance_id)
            ->first();

        if (! $entry) {
            toast()->warning('Not found', 'Temp merchant item not found.');
            return back();
        }

        $oldAttributes = $entry->getAttributes();

        MerchantlistTemp::where('npcid', $merchant)
            ->where('slot', $slot)
            ->where('zone_id', $zone_id)
            ->where('instance_id', $instance_id)
            ->delete();

        activity()
            ->useLog('merchants')
            ->tap(function ($activity) use ($merchant) {
                $activity->subject_id = $merchant;
                $activity->subject_type = MerchantlistTemp::class;
            })
            ->event('deleted')
            ->withProperties([
                'old' => $oldAttributes,
            ])
            ->log('deleted');

        return back()->with('success', 'Temp item removed');
    }

    public function clearAll(Request $request, $merchant)
    {
        $query = MerchantlistTemp::where('npcid', $merchant);
        $count = $query->count();

        if ($count === 0) {
            return back()->with('info', 'No items to clear.');
        }

        $query->delete();

        activity()
            ->useLog('merchants')
            ->tap(function($activity) use ($merchant) {
                $activity->subject_id = $merchant;
                $activity->subject_type = MerchantlistTemp::class;
            })
            ->event('deleted')
            ->withProperties([
                'count' => $count,
                'npc_id' => $merchant
            ])
            ->log("Cleared all ({$count}) temporary merchant items");

        return back()->with('success', "Successfully cleared {$count} temporary items.");
    }
}
