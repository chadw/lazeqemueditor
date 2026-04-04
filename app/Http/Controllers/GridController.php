<?php

namespace App\Http\Controllers;

use App\Models\Grid;
use Illuminate\Http\Request;

class GridController extends Controller
{
    public function store(Request $request, int $zoneid)
    {
        $validated = $request->validate([
            'id'    => 'required|integer|min:1',
            'type'  => 'required|integer',
            'type2' => 'required|integer',
        ]);

        Grid::create([
            'zoneid' => $zoneid,
            'id'     => $validated['id'],
            'type'   => $validated['type'],
            'type2'  => $validated['type2'],
        ]);

        return back()->with('success', 'Grid created.');
    }

    public function update(Request $request, int $zoneid, int $gridid)
    {
        $grid = Grid::where('zoneid', $zoneid)
            ->where('id', $gridid)
            ->firstOrFail();

        $validated = $request->validate([
            'type'  => 'required|integer',
            'type2' => 'required|integer',
        ]);

        $grid->update($validated);

        return back()->with('success', 'Grid updated.');
    }

    public function destroy(int $zoneid, int $gridid)
    {
        $grid = Grid::where('zoneid', $zoneid)
            ->where('id', $gridid)
            ->firstOrFail();

        $grid->entries()->delete();

        $grid->delete();

        return back()->with('success', 'Grid deleted.');
    }
}
