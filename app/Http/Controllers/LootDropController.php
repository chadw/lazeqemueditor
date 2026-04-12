<?php

namespace App\Http\Controllers;

use App\Filters\LootDropFilter;
use App\Http\Requests\LootDropRequest;
use App\Models\LootDrop;
use App\Models\LootTable;
use App\Models\LoottableEntry;
use App\Models\LootdropEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Spatie\DiscordAlerts\Facades\DiscordAlert;

class LootDropController extends Controller
{
    /**
     * index of all loot drops
     *
     * @param  mixed $request
     * @return void
     */
    public function index(Request $request)
    {
        $query = LootDrop::query();

        $drops = (new LootDropFilter($request))
            ->apply($query)
            ->withCount(['entries', 'loottableEntries'])
            ->orderBy('id')
            ->paginate(50)
            ->withQueryString();

        return view('loot.drops.index', compact('drops'));
    }

    /**
     * edit loot drop
     *
     * @param  mixed $drop
     * @return void
     */
    public function edit(LootDrop $drop)
    {
        $drop->load('entries.item');

        $loottable = null;
        $lte = LoottableEntry::where('lootdrop_id', $drop->id)->first();
        if ($lte) {
            $loottable = LootTable::with('npcs')->find($lte->loottable_id);
        }

        return view('loot.drops.edit', compact('drop', 'loottable'));
    }

    /**
     * create a new LootDrop and automatically attach it to a LootTable
     *
     * @param  mixed $loottable
     * @param  mixed $request
     * @return void
     */
    public function store(LootTable $loottable, LootDropRequest $request)
    {
        $data = $request->validated();

        DB::connection('eqemu')->transaction(function () use ($data, $loottable) {
            if (!empty($data['lootdrop_id'])) {
                $lootdropId = $data['lootdrop_id'];
            } else {
                $drop = LootDrop::create([
                    'name' => $data['name'] ?? 'System Created (' . now()->format('Y-m-d') . ')',
                ]);
                $lootdropId = $drop->id;
            }

            LoottableEntry::create([
                'loottable_id' => $loottable->id,
                'lootdrop_id'  => $lootdropId,
            ]);
        });

        return back()->with('success', !empty($data['lootdrop_id'])
            ? 'Loot Drop linked successfully!'
            : 'New Loot Drop created and attached successfully!');
    }

    /**
     * Link an existing LootDrop to a LootTable
     *
     * @param  \App\Models\LootTable  $loottable
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function link(LootTable $loottable, Request $request)
    {
        $data = $request->validate([
            'lootdrop_id' => 'required|integer|exists:eqemu.lootdrop,id',
        ]);

        DB::connection('eqemu')->transaction(function () use ($data, $loottable) {
            LoottableEntry::firstOrCreate([
                'loottable_id' => $loottable->id,
                'lootdrop_id' => $data['lootdrop_id'],
            ]);

            toast()->success('Saved!', "Loot Drop linked successfully!");
        });

        return back();
    }

    /**
     * update loot drop
     *
     * @param  mixed $request
     * @param  mixed $drop
     * @return void
     */
    public function update(LootDropRequest $request, LootDrop $drop)
    {
        $data = $request->validated();

        $drop->update($data);
        toast()->success('Saved!', "Loot drop updated.");

        return back()->with('success', 'Loot drop updated.');
    }

    /**
     * clone this loot drop and all its entries
     *
     * @param  mixed $drop
     * @return void
     */
    public function clone(LootDrop $drop)
    {
        $newDrop = null;

        DB::connection('eqemu')->transaction(function () use ($drop, &$newDrop) {
            EloquentModel::withoutEvents(function () use ($drop, &$newDrop) {
                $dropData = Arr::except($drop->toArray(), ['id']);
                $dropData['name'] = ($dropData['name'] ?? 'LootDrop') . ' (copy)';
                $newDrop = LootDrop::create($dropData);

                $counts = [
                    'lootdrop_entries' => 0,
                ];

                $drop->load('entries');

                foreach ($drop->entries as $entry) {
                    $entryData = Arr::except($entry->toArray(), ['lootdrop_id']);
                    $entryData['lootdrop_id'] = $newDrop->id;
                    LootdropEntry::create($entryData);
                    $counts['lootdrop_entries']++;
                }

                $newDrop->setAttribute('clone_counts', $counts);
            });
        });

        if ($newDrop) {
            $counts = $newDrop->getAttribute('clone_counts') ?? [];

            $summary = sprintf(
                "Created LootDrop id=%s name=%s; LootdropEntries=%d",
                $newDrop->id ?? 'Unknown',
                $newDrop->name,
                $counts['lootdrop_entries'] ?? 0,
            );

            try {
                DiscordAlert::message("[CREATED CLONE] " . $summary);
            } catch (\Throwable $e) {
            }

            toast()->success('Cloned', 'Loot Drop cloned as [' . $newDrop->name . ']');
            return redirect()->route('loot.drops.edit', $newDrop);
        }

        return back()->withErrors('Failed to clone loot drop');
    }

    /**
     * unlink this LootDrop from all LootTable entries
     *
     * @param  mixed $drop
     * @return void
     */
    public function unlink(LootDrop $drop)
    {
        $count = LoottableEntry::where('lootdrop_id', $drop->id)
            ->delete();

        if ($count) {
            toast()->success('Unlinked', "Unlinked {$count} LootTable entry(s) from LootDrop [{$drop->name}]");
        } else {
            toast()->info('No action', 'No Loot Tables were using this Loot Drop.');
        }

        return back();
    }

    /**
     * delete a loot drop and all related entries.
     *
     * @param  mixed $drop
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(LootDrop $drop)
    {
        $counts = [
            'lootdrop_entries' => 0,
            'loottable_entries' => 0,
        ];

        DB::connection('eqemu')->transaction(function () use ($drop, &$counts) {
            EloquentModel::withoutEvents(function () use ($drop, &$counts) {
                $counts['lootdrop_entries'] = LootdropEntry::where('lootdrop_id', $drop->id)->count();
                LootdropEntry::where('lootdrop_id', $drop->id)->delete();

                $counts['loottable_entries'] = LoottableEntry::where('lootdrop_id', $drop->id)->count();
                LoottableEntry::where('lootdrop_id', $drop->id)->delete();

                $drop->delete();
            });
        });

        $summary = sprintf(
            "Deleted LootDrop id=%s name=%s; LootdropEntries=%d; LoottableEntries=%d",
            $drop->id ?? 'Unknown',
            $drop->name,
            $counts['lootdrop_entries'] ?? 0,
            $counts['loottable_entries'] ?? 0,
        );

        try {
            DiscordAlert::message("[DELETED] " . $summary);
        } catch (\Throwable $e) {
        }

        toast()->success('Deleted', 'Loot Drop removed: ' . $drop->name);
        return redirect()->route('loot.drops.index');
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

        return LootDrop::query()
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
