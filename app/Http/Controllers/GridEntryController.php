<?php

namespace App\Http\Controllers;

use App\Models\GridEntry;
use Illuminate\Http\Request;

class GridEntryController extends Controller
{
    public function store(Request $request, int $zoneid, int $gridid)
    {
        $validated = $request->validate([
            'number'      => 'required|integer|min:1',
            'x'           => 'required|numeric',
            'y'           => 'required|numeric',
            'z'           => 'required|numeric',
            'heading'     => 'required|numeric',
            'pause'       => 'required|integer|min:0',
            'centerpoint' => 'boolean',
        ]);

        GridEntry::create([
            'zoneid'      => $zoneid,
            'gridid'      => $gridid,
            'number'      => $validated['number'],
            'x'           => $validated['x'],
            'y'           => $validated['y'],
            'z'           => $validated['z'],
            'heading'     => $validated['heading'],
            'pause'       => $validated['pause'],
            'centerpoint' => $validated['centerpoint'] ?? false,
        ]);

        return back()->with('success', 'Grid entry created.');
    }

    public function update(Request $request, int $zoneid, int $gridid, int $number)
    {
        $entry = GridEntry::where('zoneid', $zoneid)
            ->where('gridid', $gridid)
            ->where('number', $number)
            ->firstOrFail();

        $validated = $request->validate([
            'x'           => 'required|numeric',
            'y'           => 'required|numeric',
            'z'           => 'required|numeric',
            'heading'     => 'required|numeric',
            'pause'       => 'required|integer|min:0',
            'centerpoint' => 'boolean',
        ]);

        $entry->update($validated);

        return back()->with('success', 'Grid entry updated.');
    }

    public function destroy(int $zoneid, int $gridid, int $number)
    {
        GridEntry::where('zoneid', $zoneid)
            ->where('gridid', $gridid)
            ->where('number', $number)
            ->delete();

        return back()->with('success', 'Grid entry deleted.');
    }
}
