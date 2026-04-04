<?php

namespace App\Http\Controllers;

use App\Models\LootDrop;
use App\Models\LootTable;
use Illuminate\Http\Request;
use App\Models\LoottableEntry;
use App\Http\Requests\UpdateLootTableEntryRequest;

class LootTableEntryController extends Controller
{
    public function store(Request $request, LootTable $loottable)
    {
        $data = $request->validate([
            'lootdrop_id' => 'required|integer|exists:eqemu.lootdrop,id',
            'probability' => 'required|numeric|min:0|max:100',
            'droplimit'   => 'nullable|integer|min:0',
            'mindrop'     => 'nullable|integer|min:0',
            'multiplier'  => 'nullable|integer|min:1',
        ]);

        $data['loottable_id'] = $loottable->id;

        LoottableEntry::create($data);

        return back()->with('success', 'Loot drop added to table');
    }

    public function update(UpdateLootTableEntryRequest $request, LootTable $loottable, LootDrop $lootdrop) {
        //dd($request->all(), $loottable, $lootdrop);

        $loottable->loottableEntries()
            ->where('lootdrop_id', $lootdrop->id)
            ->update($request->input('entry'));

        $lootdrop->update($request->input('lootdrop'));

        return back()->with('success', 'Loot drop updated');
    }

    public function destroy(LootTable $loottable, $lootdrop_id)
    {
        $entry = LoottableEntry::where('loottable_id', $loottable->id)
            ->where('lootdrop_id', $lootdrop_id)
            ->first();

        if ($entry) {
            $entry->setKeyName('lootdrop_id');
            $entry->setAttribute('lootdrop_id', $lootdrop_id);
            $entry->delete();
        }

        return back()->with('success', 'Loot table entry removed');
    }
}
