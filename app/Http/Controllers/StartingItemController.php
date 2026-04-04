<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use App\Models\StartingItem;
use Illuminate\Http\Request;
use App\Http\Requests\StartingItemRequest;

class StartingItemController extends Controller
{
    public function index(Request $request)
    {
        $startingItems = StartingItem::with('item')
            ->orderBy('id')
            ->paginate(100)
            ->withQueryString();

        $zones = Zone::selectZones();

        return view('starting-items.index', compact('startingItems', 'zones'));
    }

    public function store(StartingItemRequest $request)
    {
        $model = StartingItem::create($request->validated());
        toast()->success('Saved!', 'Starting Item created.');

        return response()->json([
            'success'  => true,
            'data'     => $model->fresh(),
            'redirect' => url()->previous(),
        ], 201);
    }

    public function update(StartingItemRequest $request, StartingItem $startingItem)
    {
        $startingItem->update($request->validated());
        toast()->success('Saved!', 'Starting Item updated.');

        return response()->json([
            'success'  => true,
            'data'     => $startingItem->fresh(),
            'redirect' => url()->previous(),
        ], 201);
    }

    public function destroy(StartingItem $startingItem)
    {
        $startingItem->delete();
        toast()->success('Saved!', 'Starting Item deleted.');

        return back();
    }
}
