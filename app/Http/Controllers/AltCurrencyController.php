<?php

namespace App\Http\Controllers;

use App\Filters\CharacterAltCurrencyFilter;
use App\Http\Requests\AlternateCurrencyRequest;
use App\Http\Requests\CharacterAltCurrencyRequest;
use App\Jobs\RefreshAltCurrencyCache;
use App\Models\AlternateCurrency;
use App\Models\CharacterAltCurrency;
use App\Models\NpcType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AltCurrencyController extends Controller
{
    public function index(Request $request)
    {
        $altcurrency = AlternateCurrency::with('item')
            ->sortable(['id' => 'asc'])
            ->paginate(100)
            ->withQueryString();

        return view('alt-currency.index', compact('altcurrency'));
    }

    public function npcs(Request $request)
    {
        $altCurrency = AlternateCurrency::with('item')
            ->orderBy('id', 'asc')
            ->get();

        $altNpcs = NpcType::select('id', 'name', 'alt_currency_id')
            ->where('alt_currency_id', '>', 0)
            ->with('firstSpawnEntries.spawn2.zoneData')
            ->sortable(['id' => 'asc'])
            ->paginate(100)
            ->withQueryString();

        return view('alt-currency.npcs', compact('altCurrency', 'altNpcs'));
    }

    public function characters(Request $request)
    {
        $altCurrency = AlternateCurrency::with('item')
            ->orderBy('id', 'asc')
            ->get();

        $query = CharacterAltCurrency::query()
            ->with('character')
            ->select('char_id', 'currency_id', 'amount')
            ->where('amount', '>', 0);

        $altChars = (new CharacterAltCurrencyFilter($request))
            ->apply($query)
            ->sortable(['char_id' => 'asc'])
            ->paginate(100)
            ->withQueryString();

        return view('alt-currency.characters', compact('altCurrency', 'altChars'));
    }

    public function store(AlternateCurrencyRequest $request)
    {
        $data = $request->validated();
        $data['id'] = AlternateCurrency::max('id') + 1;

        $model = AlternateCurrency::create($data);

        try {
            Cache::forget('alt_currency');
            RefreshAltCurrencyCache::dispatch();
        } catch (\Throwable $e) {
        }

        toast()->success('Saved!', 'Alt Currency created.');

        return response()->json([
            'success'  => true,
            'data'     => $model,
            'redirect' => route('alt-currency.index'),
            'message'  => 'Alt Currency created.',
        ], 201);
    }

    public function update(AlternateCurrencyRequest $request, AlternateCurrency $alternateCurrency): JsonResponse
    {
        $data = $request->validated();

        $alternateCurrency->update($data);

        try {
            Cache::forget('alt_currency');
            RefreshAltCurrencyCache::dispatch();
        } catch (\Throwable $e) {
        }

        toast()->success('Saved!', 'Alt Currency updated.');

        return response()->json([
            'success' => true,
            'data'    => $alternateCurrency,
            'redirect' => route('alt-currency.index'),
            'message' => 'Alt Currency updated.',
        ], 201);
    }

    public function destroy(AlternateCurrency $alternateCurrency)
    {
        $alternateCurrency->delete();

        try {
            Cache::forget('alt_currency');
            RefreshAltCurrencyCache::dispatch();
        } catch (\Throwable $e) {
        }

        return back()->with('success', 'Alt Currency deleted.');
    }

    public function storeNpc(Request $request)
    {
        $npc = NpcType::findOrFail($request->npc_id);

        $npc->alt_currency_id = (int) $request->alt_currency_id;
        $npc->save();

        toast()->success('Saved!', 'Alt Currency NPC created.');

        return response()->json([
            'success'  => true,
            'data'     => $npc,
            'redirect' => url()->previous(),
            'message'  => 'Alt Currency NPC created.',
        ], 201);

        return back();
    }

    public function updateNpc(Request $request, NpcType $npc)
    {
        $npc->alt_currency_id = $request->alt_currency_id;
        $npc->save();

        toast()->success('Saved!', 'Alt Currency NPC updated.');

        return response()->json([
            'success'  => true,
            'data'     => $npc,
            'redirect' => url()->previous(),
            'message'  => 'Alt Currency NPC updated.',
        ], 201);

        return back();
    }

    public function destroyNpc(NpcType $npc)
    {
        $npc->alt_currency_id = 0;
        $npc->save();

        toast()->success('Saved!', 'Alt Currency NPC removed.');

        return back();
    }

    public function storeCharacter(CharacterAltCurrencyRequest $request)
    {
        $model = CharacterAltCurrency::create($request->validated());

        toast()->success('Saved!', 'Character Alt Currency created.');

        return response()->json([
            'success'  => true,
            'data'     => $model,
            'redirect' => url()->previous(),
            'message'  => 'Character Alt Currency created.',
        ], 201);

        return back();
    }

    public function updateCharacter(CharacterAltCurrencyRequest $request, CharacterAltCurrency $character)
    {
        $character->update($request->validated());

        toast()->success('Saved!', 'Character Alt Currency updated.');

        return response()->json([
            'success'  => true,
            'data'     => $character,
            'redirect' => url()->previous(),
            'message'  => 'Character Alt Currency updated.',
        ], 201);

        return back();
    }

    public function destroyCharacter(int $char_id, int $currency_id)
    {
        $currency = CharacterAltCurrency::where('char_id', $char_id)
            ->where('currency_id', $currency_id)
            ->firstOrFail();

        $currency->triggerEvent('deleted');

        CharacterAltCurrency::where('char_id', $char_id)
            ->where('currency_id', $currency_id)
            ->delete();

        toast()->success('Saved!', 'Character Alt Currency deleted.');

        return back();
    }
}
