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
        StartingItem::create($request->validated());

        return back()->with('success', 'Starting Item created.');
    }

    public function update(StartingItemRequest $request, StartingItem $startingItem)
    {
        $startingItem->update($request->validated());

        return back()->with('success', 'Starting Item updated.');
    }

    public function destroy(StartingItem $startingItem)
    {
        $startingItem->delete();

        return back()->with('success', 'Starting Item deleted.');
    }
}
