<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use App\Models\NpcType;
use App\Models\Merchantlist;
use App\Filters\MerchantFilter;
use App\Models\AlternateCurrency;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Throwable;
use App\Http\Requests\MerchantlistRequest;

class MerchantController extends Controller
{
    public function index(Request $request)
    {
        $selectedZoneId = $request->query('zone');
        $selectedVersion = $request->query('v', 0);
        $selectedNpcId = $request->query('npc');

        $zones = Zone::baseZones();
        $versions = [];
        $npcs = collect();
        $merchantItems = collect();
        $selectedNpc = null;
        $merchantAltCurrency = null;

        if ($selectedZoneId) {
            $zone = Zone::where('zoneidnumber', $selectedZoneId)->firstOrFail();

            $versions = Zone::where('zoneidnumber', $selectedZoneId)
                ->orderBy('version')
                ->get(['version']);

            $npcs = NpcType::getForZone($zone, (int) $selectedVersion, true);

            $zoneMerchantIds = $npcs->pluck('merchant_id')->filter()->unique()->values()->all();
            if (!empty($zoneMerchantIds)) {
                $zoneCounts = Merchantlist::whereIn('merchantid', $zoneMerchantIds)
                    ->selectRaw('merchantid, count(*) as cnt')
                    ->groupBy('merchantid')
                    ->get()
                    ->pluck('cnt', 'merchantid');

                $npcs = $npcs->map(function ($npc) use ($zoneCounts) {
                    $npc->setAttribute('merchant_item_count', (int) ($zoneCounts->get($npc->merchant_id) ?? 0));
                    return $npc;
                });
            }

            if ($selectedNpcId) {
                $selectedNpc = NpcType::find($selectedNpcId);

                if ($selectedNpc && $selectedNpc->merchant_id > 0) {
                    $merchantItems = Merchantlist::where('merchantid', $selectedNpc->merchant_id)
                        ->with('items')
                        ->sortable(['slot' => 'asc'])
                        ->paginate(100)
                        ->withQueryString();

                    if (!empty($selectedNpc->alt_currency_id)) {
                        $merchantAltCurrency = AlternateCurrency::with('item')->find($selectedNpc->alt_currency_id);
                    }
                }
            }
        }

        if (trim((string) $request->input('merchant', '')) !== '') {
            $lists = (new MerchantFilter($request))
                ->apply(Merchantlist::with('npc'))
                ->get();

            $counts = $lists->groupBy('merchantid')->map(fn($g) => $g->count());

            $npcs = $lists->pluck('npc')
                ->filter()
                ->unique('id')
                ->sortBy(fn($npc) => $npc->clean_name)
                ->values()
                ->map(function ($npc) use ($counts) {
                    $npc->merchant_item_count = $counts->get($npc->merchant_id) ?? 0;
                    return $npc;
                });
                //dd($lists, $npcs);
        }

        if (!$selectedZoneId && $selectedNpcId) {
            $selectedNpc = NpcType::find($selectedNpcId);

            if ($selectedNpc && $selectedNpc->merchant_id > 0) {
                $merchantItems = Merchantlist::where('merchantid', $selectedNpc->merchant_id)
                    ->with('items')
                    ->sortable(['slot' => 'asc'])
                    ->paginate(100)
                    ->withQueryString();

                if (!empty($selectedNpc->alt_currency_id)) {
                    $merchantAltCurrency = AlternateCurrency::with('item')->find($selectedNpc->alt_currency_id);
                }
            }
        }

        return view('merchants.index', compact(
            'zones',
            'versions',
            'npcs',
            'merchantItems',
            'selectedNpc',
            'merchantAltCurrency'
        ));
    }

    public function store(MerchantlistRequest $request, NpcType $npc)
    {
        $data = $request->validated();

        $merchantId = (int) ($npc->merchant_id ?? 0);
        if ($merchantId <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Selected NPC does not have a merchant id'
            ], 422);
        }

        $data['merchantid'] = $merchantId;

        $tries = 0;
        $maxTries = 5;
        do {
            $tries++;

            if (empty($data['slot'])) {
                $maxSlot = Merchantlist::where('merchantid', $merchantId)->max('slot');
                $data['slot'] = max(1, (int) $maxSlot + 1);
            }

            try {
                $model = Merchantlist::create($data);

                toast()->success('Saved!', 'Merchant Item created.');

                return response()->json([
                    'success'  => true,
                    'data'     => $model,
                    'redirect' => url()->previous(),
                ], 201);
            } catch (QueryException $ex) {
                if ($tries >= $maxTries) {
                    throw $ex;
                }
                unset($data['slot']);
            } catch (Throwable $t) {
                throw $t;
            }
        } while ($tries < $maxTries);

        return response()->json(['success' => false, 'message' => 'Unable to create merchant item'], 500);
    }

    public function update(MerchantlistRequest $request, $id)
    {
        $data = $request->validated();

        $entry = Merchantlist::where('merchantid', $id)
            ->where('slot', $request->slot)
            ->firstOrFail();

        $oldAttributes = $entry->getAttributes();
        $changesOnly = collect($data)->filter(function ($value, $key) use ($oldAttributes) {
            return isset($oldAttributes[$key]) && (string)$oldAttributes[$key] !== (string)$value;
        })->toArray();

        if (empty($changesOnly)) {
            return back()->with('info', 'No changes were made.');
        }

        Merchantlist::where('merchantid', $id)
            ->where('slot', $request->slot)
            ->update($changesOnly);

        $entry->fill($changesOnly);
        $updated = $entry;

        activity()
            ->performedOn($entry)
            ->useLog('merchants')
            ->tap(fn($activity) => $activity->subject_id = $id)
            ->event('updated')
            ->withProperties([
                'attributes' => $changesOnly,
                'old' => array_intersect_key($oldAttributes, $changesOnly)
            ])
            ->log('updated');

        toast()->success('Saved!', 'Merchant Item updated.');

        return response()->json([
            'success'  => true,
            'data'     => $updated,
            'redirect' => url()->previous(),
        ], 201);
    }

    public function destroy(Request $request, $id, $slot)
    {
        $entry = Merchantlist::where('merchantid', $id)
            ->where('slot', $slot)
            ->first();

        if (!$entry) {
            toast()->warning('Not found', 'Merchant Item not found.');
            return back();
        }

        $oldAttributes = $entry->getAttributes();

        Merchantlist::where('merchantid', $id)
            ->where('slot', $slot)
            ->delete();

        activity()
            ->performedOn($entry)
            ->useLog('merchants')
            ->tap(fn($activity) => $activity->subject_id = $id)
            ->event('deleted')
            ->withProperties([
                'old' => $oldAttributes,
            ])
            ->log('deleted');

        toast()->success('Saved!', 'Merchant Item deleted.');

        return back();
    }
}
