<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\HorseRequest;
use App\Models\Horse;

class MountController extends Controller
{
    public function index(Request $request)
    {
        //$mounts = Horse::with('npc')->paginate(50);
        $mounts = Horse::sortable('filename', 'asc')
            ->paginate(50);

        return view('mounts.index', compact('mounts'));
    }

    public function store(HorseRequest $request)
    {
        Horse::create($request->validated());

        toast()->success('Saved!', 'Mount created.');

        return back();
    }

    public function update(HorseRequest $request, Horse $horse)
    {
        $horse->update($request->validated());

        toast()->success('Saved!', 'Mount updated.');

        return back();
    }

    public function destroy(Horse $horse)
    {
        $horse->delete();

        toast()->success('Saved!', 'Mount deleted.');

        return back();
    }
}
