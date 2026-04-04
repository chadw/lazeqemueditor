<?php

namespace App\Http\Controllers;

use App\Http\Requests\LootTableRequest;
use App\Models\LootTable;
use App\Models\NpcType;
use Illuminate\Http\Request;
use App\Filters\LootTableFilter;
use App\Models\LootDrop;
use App\Models\LootdropEntry;
use App\Models\LoottableEntry;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Spatie\DiscordAlerts\Facades\DiscordAlert;

class LootTableController extends Controller
{
    public function index()
    {
        $query = LootTable::withCount('loottableEntries')
            ->withCount('npcs')
            ->orderBy('id');

        $tables = (new LootTableFilter(request()))
            ->apply($query)
            ->paginate(50)
            ->withQueryString();

        return view('loot.index', compact('tables'));
    }

    public function edit(LootTable $loottable)
    {
        $loottable->load([
            //'loottableEntries.lootdrop',
            'loottableEntries.lootdropEntries.item',
            'npcs:id,name,loottable_id'
        ]);

        return view('loot.edit', [
            'table' => $loottable
        ]);
    }

    public function store(LootTableRequest $request)
    {
        $data = $request->validated();
        $loottable = LootTable::create($data);

        $assigned = false;
        if (!empty($data['npc_id'])) {
            $assigned = (bool) NpcType::where('id', $data['npc_id'])->update([
                'loottable_id' => $loottable->id
            ]);
        }

        $message = "Loot Table <b>[%s]</b> created" . ($assigned ? " and assigned to NPC." : ".");
        toast()->success('Saved!', $message, [$loottable->name]);

        return back();
    }

    public function update(LootTableRequest $request, LootTable $loottable)
    {
        $data = $request->validated();

        $loottable->update($data);

        return back()->with('success', 'Loot Table [' . $loottable->name . '] updated');
    }

    /**
     * destroy loottable unless lootdrops are associated
     * @TODO add an override to allow deleting regardless, with a warning of course
     *
     * @param  mixed $loottable
     * @return void
     */
    public function destroy(LootTable $loottable)
    {
        if ($loottable->loottableEntries()->exists()) {
            toast()->error('Oops', 'Loot table has entries and cannot be deleted.');
            return back();
        }

        $loottable->delete();

        return redirect()->route('loot.index')
            ->with('success', 'Loot table deleted');
    }

    public function unlink(LootTable $loottable)
    {
        $count = NpcType::where('loottable_id', $loottable->id)
            ->update(['loottable_id' => 0]);

        if ($count) {
            toast()->success('Unlinked', "Unlinked {$count} NPC(s) from Loot Table [{$loottable->name}]");
        } else {
            toast()->info('No action', 'No NPCs were using this loot table.');
        }

        return back();
    }

    public function clone(LootTable $loottable)
    {
        $newTable = null;

        DB::connection('eqemu')->transaction(function () use ($loottable, &$newTable) {
            EloquentModel::withoutEvents(function () use ($loottable, &$newTable) {
                $tableData = Arr::except($loottable->toArray(), ['id']);
                $tableData['name'] = $tableData['name'] . ' (copy)';
                $newTable = LootTable::create($tableData);

                $loottable->load('loottableEntries.lootdrop.entries');

                $counts = [
                    'loottable_entries' => 0,
                    'lootdrops' => 0,
                    'lootdrop_entries' => 0,
                ];

                foreach ($loottable->loottableEntries as $entry) {
                    $newLootdropId = null;

                    if ($entry->lootdrop) {
                        $drop = $entry->lootdrop;
                        $dropData = Arr::except($drop->toArray(), ['id']);
                        $dropData['name'] = ($dropData['name'] ?? 'LootDrop') . ' (copy)';
                        $newDrop = LootDrop::create($dropData);
                        $counts['lootdrops']++;

                        // clone drop entries
                        foreach ($drop->entries as $dropEntry) {
                            $dropEntryData = Arr::except($dropEntry->toArray(), ['lootdrop_id']);
                            $dropEntryData['lootdrop_id'] = $newDrop->id;
                            LootdropEntry::create($dropEntryData);
                            $counts['lootdrop_entries']++;
                        }

                        $newLootdropId = $newDrop->id;
                    }

                    // create loottable entry referencing new drop
                    $entryData = Arr::only($entry->toArray(), ['multiplier', 'droplimit', 'mindrop', 'probability']);
                    $entryData['loottable_id'] = $newTable->id;
                    $entryData['lootdrop_id'] = $newLootdropId;
                    LoottableEntry::create($entryData);
                    $counts['loottable_entries']++;
                }

                // attach counts to the newTable for use after the transaction
                $newTable->setAttribute('clone_counts', $counts);
            });
        });

        if ($newTable) {
            $counts = $newTable->getAttribute('clone_counts') ?? [];

            $summary = sprintf(
                "Created LootTable id=%s name=%s; LootDrops=%d; LootdropEntries=%d; LoottableEntries=%d",
                $newTable->id ?? 'Unknown',
                $newTable->name,
                $counts['lootdrops'] ?? 0,
                $counts['lootdrop_entries'] ?? 0,
                $counts['loottable_entries'] ?? 0,
            );

            try {
                DiscordAlert::message("[CREATED CLONE] " . $summary);
            } catch (\Throwable $e) {
            }

            toast()->success('Cloned', 'Loot Table cloned as [' . $newTable->name . ']');
            return redirect()->route('loot.edit', $newTable);
        }

        return back()->withErrors('Failed to clone loot table');
    }

    /**
     * search for ajaxSelect
     *
     * @param  mixed $request
     * @return void
     */
    public function search(Request $request)
    {
        $search = $request->string('q');

        return LootTable::query()
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
