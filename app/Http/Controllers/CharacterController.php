<?php

namespace App\Http\Controllers;

use App\Filters\CharacterFilter;
use App\Filters\CharRecipeFilter;
use App\Http\Requests\CharacterDataRequest;
use App\Models\CharacterData;
use App\Models\CharRecipeList;
use App\Models\Zone;
use App\Services\CharacterStats;
use Illuminate\Http\Request;

class CharacterController extends Controller
{
    public function index(Request $request)
    {
        $sortable = ['id', 'name', 'account', 'race', 'class', 'level', 'guild', 'birthday', 'last_login', 'time_played'];
        $sort = $request->input('sort', 'id');
        $direction = strtolower($request->input('direction', 'desc')) === 'asc' ? 'asc' : 'desc';

        if (!in_array($sort, $sortable)) {
            $sort = 'id';
        }

        $query = CharacterData::query();
        $query = (new CharacterFilter($request))->apply($query);

        if ($sort === 'account') {
            $query = $query->leftJoin('account as a', 'a.id', '=', 'character_data.account_id')
                ->select('character_data.*')
                ->orderBy('a.name', $direction);
        } elseif ($sort === 'guild') {
            $query = $query->leftJoin('guild_members as gm', 'gm.char_id', '=', 'character_data.id')
                ->leftJoin('guilds as g', 'g.id', '=', 'gm.guild_id')
                ->select('character_data.*')
                ->orderBy('g.name', $direction);
        } else {
            $query = $query->orderBy("character_data.{$sort}", $direction);
        }

        $characters = $query
            ->with(['account', 'stats', 'guildMember.guild'])
            ->paginate(50)
            ->withQueryString();

        return view('characters.index', compact('characters'));
    }

    public function show(CharacterData $character)
    {
        $character->load([
            'account',
            'stats',
            'guildMember.guild',
            'corpses.zone',
            'corpses.corpseItems',
            'altCurrency.altCurrency.item',
            'currency',
            'keys.item',
            'skills',
            'languages',
            'faction.character',
            'faction.faction',
            'aa',
            //'traders',
            'inventory' => function ($q) {
                $q->with([
                    'item.wornEffectSpell',
                    'aug1.wornEffectSpell',
                    'aug2.wornEffectSpell',
                    'aug3.wornEffectSpell',
                    'aug4.wornEffectSpell',
                    'aug5.wornEffectSpell',
                    'aug6.wornEffectSpell',
                ]);
            },
            'sharedbank.item',
            'bindpoint.zone',
            'zone',
            'lockouts' => fn($q) => $q->orderBy('expire_time'),
            'tribute._tribute',
            'disciplines.spell',
            'spells.spell',
            'memmedSpells.spell',
            'bandolier.item',
        ]);

        $items = collect([
            'gear' => collect(),
            'shared_bank' => collect(),
            'shared_bank_items' => collect(),
            'bags' => collect(),
            'bag_items' => collect(),
            'bank' => collect(),
            'bank_items' => collect(),
        ]);

        if ($character->inventory && $character->inventory->count()) {
            $items = collect([
                'gear' => $character->inventory->whereBetween('slot_id', [
                    config('everquest.slot_equipment_start'),
                    config('everquest.slot_equipment_end')
                ]),
                'shared_bank' => $character->sharedbank->filter(
                    fn($inv) =>
                    $inv->slot_id >= config('everquest.slot_sharedbank_start') &&
                        $inv->slot_id <= config('everquest.slot_sharedbank_end')
                ),
                'shared_bank_items' => $character->sharedbank->filter(
                    fn($inv) =>
                    $inv->slot_id >= config('everquest.slot_sharedbank_bag_start') &&
                        $inv->slot_id <= config('everquest.slot_sharedbank_bag_end')
                ),
                'bags' => $character->inventory->filter(
                    fn($inv) =>
                    $inv->slot_id >= config('everquest.slot_inventory_start') &&
                        $inv->slot_id <= config('everquest.slot_inventory_end')
                ),
                'bag_items' => $character->inventory->filter(
                    fn($inv) =>
                    $inv->slot_id >= config('everquest.slot_inventory_bags_start') &&
                        $inv->slot_id <= config('everquest.slot_inventory_bags_end')
                ),
                'bank' => $character->inventory->filter(
                    fn($inv) =>
                    $inv->slot_id >= config('everquest.slot_bank_start') &&
                        $inv->slot_id <= config('everquest.slot_bank_end')
                ),
                'bank_items' => $character->inventory->filter(
                    fn($inv) =>
                    $inv->slot_id >= config('everquest.slot_bank_bags_start') &&
                        $inv->slot_id <= config('everquest.slot_bank_bags_end')
                ),
            ]);
            //dd($items);
        }

        $stats = CharacterStats::calculate(collect($items['gear']));
        $zones = Zone::selectZones();

        return view('characters.show', compact('character', 'stats', 'items', 'zones'));
    }

    public function recipes(Request $request, CharRecipeFilter $filter)
    {
        $recipes = $filter->apply(
            CharRecipeList::query()
                ->with(['character', 'recipe']),
            $request->only(['character', 'recipe'])
        )
            ->orderByDesc('madecount')
            ->paginate(25)
            ->withQueryString();

        return view('characters.recipes.index', compact('recipes'));
    }

    public function edit(CharacterData $character) {}

    public function store(CharacterDataRequest $request) {}

    public function update(CharacterDataRequest $request, CharacterData $character) {}

    public function destroy(CharacterData $character) {}

    public function move(Request $request, CharacterData $character)
    {
        $rules = [
            'account_id' => ['sometimes', 'required', 'exists:eqemu.account,id'],
            'zone_id' => ['sometimes', 'required', 'exists:eqemu.zone,zoneidnumber'],
        ];

        $data = $request->validate($rules);

        if (isset($data['zone_id'])) {
            $zone = Zone::resolveZone((int) $data['zone_id'])->first([
                'zoneidnumber',
                'short_name',
                'safe_x',
                'safe_y',
                'safe_z',
                'safe_heading'
            ]);

            $oldZoneShort = optional($character->zone)->short_name ?? $character->zone_id;
            $newZoneShort = $zone->short_name ?? $data['zone_id'];

            $character->update([
                'zone_id' => $data['zone_id'],
                'x' => $zone->safe_x ?? $character->x ?? 0,
                'y' => $zone->safe_y ?? $character->y ?? 0,
                'z' => $zone->safe_z ?? $character->z ?? 0,
                'heading' => $zone->safe_heading ?? $character->heading ?? 0,
            ]);

            toast()->success('Saved!', "Character moved from zone {$oldZoneShort} to {$newZoneShort}");

            return response()->json([
                'success' => true,
                'data'    => $character->fresh(),
                'redirect' => url()->previous(),
            ], 200);
        }

        if (isset($data['account_id'])) {
            $oldAccountId = $character->account_id;

            $character->update([
                'account_id' => $data['account_id'],
            ]);

            toast()->success('Saved!', "Character moved from account {$oldAccountId} to {$data['account_id']}");

            return response()->json([
                'success' => true,
                'data'    => $character->fresh(),
                'redirect' => url()->previous(),
            ], 200);
        }

        return response()->json(['message' => 'No action taken'], 400);
    }

    public function search(Request $request)
    {
        $search = $request->string('q');

        return CharacterData::query()
            ->select('id', 'name')
            ->when($search, function ($q) use ($search) {
                $q->where('id', $search)
                    ->orWhere('name', 'like', "%{$search}%");
            })
            ->orderBy('id')
            ->limit(50)
            ->get();
    }
}
