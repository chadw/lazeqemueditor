<?php

namespace App\Http\Controllers;

use App\Filters\NpcFilter;
use App\Http\Requests\NpcTypeRequest;
use App\Models\Grid;
use App\Models\NpcSpell;
use App\Models\NpcType;
use App\Models\Zone;
use App\Support\ObjectSprite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\DiscordAlerts\Facades\DiscordAlert;

class NpcTypeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->filled('q')) {
            $request->query->remove('zone');
            $request->query->remove('version');
        }

        $perPage = (int) $request->query('per_page', 25);
        $filter = new NpcFilter($request);

        $query = NpcType::select('id', 'name', 'class', 'level', 'hp')
            ->with(['firstSpawnEntries.spawn2.zoneData']);

        $query = $filter->apply($query);

        $paginator = $query
            ->orderBy('name')
            ->orderBy('id')
            ->paginate($perPage)
            ->withQueryString();

        $allZones = Zone::select('id', 'zoneidnumber', 'short_name', 'long_name', 'expansion', 'version')->get();

        $zoneMap = [];
        foreach ($paginator as $npc) {
            $firstEntry = $npc->firstSpawnEntries ?? null;

            // match by spawn2 data first if available but also....
            if ($firstEntry && $firstEntry->spawn2) {
                $s2 = $firstEntry->spawn2;
                $matched = $allZones->first(function ($z) use ($s2) {
                    return $z->short_name === $s2->zone && ((int)$z->version === (int)($s2->version ?? 0));
                });

                if ($matched) {
                    $zoneMap[$npc->id] = $matched->short_name;
                } else {
                    $zoneMap[$npc->id] = $s2->zone;
                }

                continue;
            }

            // match by the og ___ method as well.
            $idStr = (string) ($npc->id ?? '');
            if (strlen($idStr) > 3) {
                $prefix = (int) substr($idStr, 0, -3);

                $matched = $allZones->first(function ($z) use ($prefix) {
                    return (int)$z->zoneidnumber === $prefix && (int)$z->version === 0;
                });

                if (!$matched) {
                    $matched = $allZones->first(function ($z) use ($prefix) {
                        return (int)$z->zoneidnumber === $prefix;
                    });
                }

                if ($matched) {
                    $zoneMap[$npc->id] = $matched->short_name;
                    continue;
                }
            }
        }

        $filters = $request->only(['q', 'zone', 'version', 'per_page']);

        $zones = Zone::baseZones();
        $versions = [];
        if (!empty($filters['zone'])) {
            $versions = Zone::where('zoneidnumber', (int)$filters['zone'])
                ->where('version', '>', 0)
                ->orderBy('version')
                ->get(['version']);
        }

        return view('npcs.index', [
            'npcs' => $paginator,
            'zoneMap' => $zoneMap,
            'filters' => $filters,
            'zones' => $zones,
            'versions' => $versions,
        ]);
    }

    public function apiIndex(Request $request)
    {
        $perPage = (int) $request->query('per_page', 25);
        $query = NpcType::query()->select('id', 'name');

        $filter = new NpcFilter($request);
        $query = $filter->apply($query)->orderBy('id');

        if ($request->boolean('all') || $perPage <= 0) {
            $zone = $request->query('zone', '');
            $version = (int) $request->query('version', 0);
            $cacheKey = "npcs_for_zone_{$zone}_v{$version}";

            $items = $query->get();

            return response()->json([
                'data' => $items,
            ]);
        }

        $paginator = $query->paginate($perPage)->withQueryString();

        return response()->json([
            'data' => $paginator->items(),
            'meta' => [
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    /**
     * edit npc
     *
     * @param  mixed $request
     * @return void
     */
    public function edit(Request $request, NpcType $npc)
    {
        $selectedZoneId = $request->query('zone');
        $selectedVersion = $request->query('v', 0);
        //$selectedNpcId = $request->query('npc');

        $zones = Zone::baseZones();
        $versions = [];
        $npcs = collect();
        //$npc = null;
        $selectedNpcId = $npc->id;
        $grids = collect();

        if ($selectedZoneId) {
            $zone = Zone::where('zoneidnumber', $selectedZoneId)->firstOrFail();

            $versions = Zone::where('zoneidnumber', $selectedZoneId)
                ->orderBy('version')
                ->get(['version']);

            $npcs = NpcType::getForZone($zone, (int) $selectedVersion);

            // grids for this specific zone.
            $grids = Grid::where('zoneid', $zone->zoneidnumber)
                ->with('zone')
                //->withCount('entries')
                ->orderBy('id')
                ->get();

            if ($selectedNpcId) {
                $npc = NpcType::with([
                    'npcSpellset',
                    'npcSpellset.npcSpellEntries.spells',
                    'npcSpellset.attackProcSpell',
                    'npcSpellset.parentSet',
                    'npcSpellset.parentSet.npcSpellEntries.spells',
                    'primaryFaction.faction',
                    'factionEntries.faction',
                    'spawnEntries.spawnGroup.spawnentries',
                    'spawnEntries.spawnGroup.spawn2',
                    'spawnEntries.spawnGroup.spawn2.spawn2Disabled',
                    'spawnEntries.spawn2',
                    'spawnEntries.spawn2.spawn2Disabled',
                    'lootTable.loottableEntries.lootdropEntries.item',
                    'merchantlist.items',
                ])->find($selectedNpcId);
            }
        }

        if (!$npc && $selectedNpcId) {
            $npc = NpcType::with([
                'npcSpellset',
                'npcSpellset.npcSpellEntries.spells',
                'npcSpellset.attackProcSpell',
                'npcSpellset.parentSet',
                'npcSpellset.parentSet.npcSpellEntries.spells',
                'primaryFaction.faction',
                'factionEntries.faction',
                'spawnEntries.spawnGroup.spawnentries',
                'spawnEntries.spawnGroup.spawn2',
                'spawnEntries.spawnGroup.spawn2.spawn2Disabled',
                'spawnEntries.spawn2',
                'spawnEntries.spawn2.spawn2Disabled',
                'lootTable.loottableEntries.lootdropEntries.item',
                'merchantlist.items',
            ])->find($selectedNpcId);
        }

        // parent select
        $allNpcSpells = NpcSpell::orderBy('id')->pluck('name', 'id')->toArray();

        // find usage
        $loottableUsage = $npc?->otherCountUsing('loottable_id') ?? 0;
        $spellsetUsage = $npc?->otherCountUsing('npc_spells_id') ?? 0;
        $factionUsage = $npc?->otherCountUsing('npc_faction_id') ?? 0;

        // objects for modal
        $objectIds = ObjectSprite::ids();

        return view('npcs.edit', compact(
            'zones',
            'versions',
            'npcs',
            'npc',
            'grids',
            'allNpcSpells',
            'loottableUsage',
            'spellsetUsage',
            'factionUsage',
            'objectIds',
        ));
    }

    public function store(NpcTypeRequest $request)
    {
        $data = $request->validated();

        NpcType::create($data);

        return redirect()->back()->with('success', 'NPC created.');
    }

    /**
     * update's npc
     *
     * @param  mixed $request
     * @param  mixed $npc
     * @return void
     */
    public function update(NpcTypeRequest $request, NpcType $npc)
    {
        $input = $request->except(['_token', '_method', 'n_tabs', 'npc_tabs']);

        $newId = isset($input['id']) ? (int)$input['id'] : (int)$npc->id;
        if ($newId !== (int)$npc->id) {
            $existing = NpcType::find($newId);
            if ($existing && !$request->boolean('confirm_id_replace')) {
                return redirect()->back()->withInput()->with('id_conflict', [
                    'id' => $existing->id,
                    'name' => $existing->name,
                ]);
            }
        }

        $changes = [];
        DB::connection('eqemu')->transaction(function () use ($input, $npc, &$changes, $newId, $request) {
            if (isset($input['id']) && (int)$input['id'] !== (int)$npc->id) {
                $targetId = (int)$input['id'];
                if (NpcType::where('id', $targetId)->exists()) {
                    if ($request->boolean('confirm_id_replace')) {
                        NpcType::where('id', $targetId)->delete();
                    }
                }
            }

            $npc->fill($input);

            if (isset($input['id']) && (int)$input['id'] !== (int)$npc->getOriginal('id')) {
                $npc->id = (int)$input['id'];
            }

            $dirty = $npc->getDirty();
            foreach ($dirty as $key => $new) {
                $old = $npc->getOriginal($key);
                $changes[$key] = ['old' => $old, 'new' => $new];
            }

            if (!empty($changes)) {
                $npc->save();
            }
        });

        return redirect()
            ->route('npcs.edit', $npc->id)
            ->with('status', count($changes) ? 'NPC updated' : 'No changes detected');
    }

    public function preview(Request $request)
    {
        $data = $request->all();

        $normalize = function ($val) use (&$normalize) {
            if (!is_array($val)) {
                return $val;
            }

            $last = null;
            foreach (array_reverse($val) as $v) {
                if ($v === null) {
                    continue;
                }
                if (is_string($v) && trim($v) === '') {
                    continue;
                }
                if (is_array($v)) {
                    $last = $normalize($v);
                    if ($last !== null && $last !== '') {
                        break;
                    }
                    continue;
                }

                $last = $v;
                break;
            }

            if ($last === null && count($val) > 0) {
                $first = reset($val);
                return is_array($first) ? $normalize($first) : $first;
            }

            return $last;
        };

        $normalized = [];
        foreach ($data as $k => $v) {
            $normalized[$k] = $normalize($v);
        }

        $npc = null;
        $id = isset($normalized['id']) ? (int)$normalized['id'] : null;
        if ($id) {
            $orig = NpcType::find($id);
            if ($orig) {
                $npc = $orig->replicate();
                $npc->setRawAttributes($orig->getAttributes(), true);
            }
        }

        if (!$npc) {
            $npc = new NpcType();
        }

        foreach ($normalized as $k => $v) {
            if (is_string($v) && is_numeric($v)) {
                $v = (strpos($v, '.') !== false) ? (float)$v : (int)$v;
            }

            $npc->{$k} = $v;
        }

        $html = view('npcs.partials.preview-npc', ['npc' => $npc])->render();

        return response()->json(['html' => $html]);
    }

    /**
     * Duplicate an npc and spawns
     * Redirects to the edit page for the new npc.
     *
     * @param  mixed $request
     * @param  mixed $npc
     * @return void
     */
    public function clone(Request $request, NpcType $npc)
    {
        $new = $npc->replicate();

        $suffix = ' (Copy)';
        $newName = $npc->name . $suffix;

        if (NpcType::where('Name', $newName)->exists()) {
            $newName = $npc->name . $suffix . ' ' . now()->format('YmdHis');
        }

        $new->name = $newName;

        $newId = null;
        DB::connection('eqemu')->transaction(function () use (&$new, &$newId, $npc) {

            $conn = DB::connection('eqemu');

            // clone npc to new id
            $table = $new->getTable();
            $max = $conn->table($table)->lockForUpdate()->max('id');
            $newId = (($max ?? 0) + 1);
            $new->id = $newId;
            $new->save();

            // get all spawn entries for original npc
            $spawnEntries = $conn->table('spawnentry')
                ->where('npcID', $npc->id)
                ->get();

            $groupMap = [];
            foreach ($spawnEntries as $entry) {
                $origGroupId = $entry->spawngroupID;

                if (!isset($groupMap[$origGroupId])) {

                    $origGroup = $conn->table('spawngroup')
                        ->where('id', $origGroupId)
                        ->first();

                    if ($origGroup) {
                        // avoid name conflict because spawngroup needs distinct name
                        $baseName = $origGroup->name ?? 'spawngroup';
                        $newName = $baseName . '_copy_' . $newId;
                        $counter = 1;
                        while ($conn->table('spawngroup')->where('name', $newName)->exists()) {
                            $newName = $baseName . '_copy_' . $newId . '_' . $counter++;
                        }

                        // spawngroup
                        $groupData = [
                            'name' => $newName,
                            'spawn_limit' => $origGroup->spawn_limit ?? null,
                            'dist' => $origGroup->dist ?? null,
                            'max_x' => $origGroup->max_x ?? null,
                            'min_x' => $origGroup->min_x ?? null,
                            'max_y' => $origGroup->max_y ?? null,
                            'min_y' => $origGroup->min_y ?? null,
                            'delay' => $origGroup->delay ?? null,
                            'mindelay' => $origGroup->mindelay ?? null,
                            'despawn' => $origGroup->despawn ?? null,
                            'despawn_timer' => $origGroup->despawn_timer ?? null,
                            'wp_spawns' => $origGroup->wp_spawns ?? 0,
                        ];

                        $newGroupId = $conn->table('spawngroup')->insertGetId($groupData);

                        // spawn2
                        $spawn2Rows = $conn->table('spawn2')
                            ->where('spawngroupID', $origGroupId)
                            ->get();

                        foreach ($spawn2Rows as $s2) {
                            $s2Data = (array) $s2;
                            unset($s2Data['id']);
                            $s2Data['spawngroupID'] = $newGroupId;
                            $conn->table('spawn2')->insert($s2Data);
                        }

                        $groupMap[$origGroupId] = $newGroupId;
                    } else {
                        // no group wtf?
                        $groupMap[$origGroupId] = null;
                    }
                }

                $newGroupIdForEntry = $groupMap[$origGroupId];
                if ($newGroupIdForEntry === null) {
                    continue;
                }

                // spawnentry
                $entryData = (array) $entry;
                $entryData['spawngroupID'] = $newGroupIdForEntry;
                $entryData['npcID'] = $newId;
                $conn->table('spawnentry')->insert($entryData);
            }
        });

        try {
            $userName = auth()->user()?->name ?? 'System';
            $message = "[CLONED] [NPC] - **User**: {$userName}, **Original:** ({$npc->id}) {$npc->name}, **Cloned to:** ({$newId}) {$new->name}";
            DiscordAlert::message($message);
        } catch (\Throwable $e) {
        }

        toast()->success('Cloned!', 'NPC and related spawns cloned.');

        $redirect = $request->input('redirect', 'edit');
        if ($redirect === 'index') {
            return back()->with('new_id', $newId);
        }

        return redirect()->route('npcs.edit', $new);
    }

    /**
     * delete's npc
     *
     * @param  mixed $npc
     * @return void
     */
    public function destroy(NpcType $npc)
    {
        $attrs = $npc->getAttributes();
        $npcId = $attrs['id'] ?? $npc->id;
        $npcName = $attrs['name'] ?? $npc->name ?? 'Unknown';

        $npc->delete();

        try {
            $u = Auth::user();
            $user = $u ? $u->name : 'System';
            $message = "[DELETED] [NPC] - **User**: {$user}, **id:** {$npcId}, **name:** {$npcName}";
            DiscordAlert::message($message);
        } catch (\Throwable $e) {
        }

        $previous = url()->previous();

        if (str_ends_with($previous, "/npcs/{$npcId}/edit")) {
            return redirect()
                ->route('npcs.index')
                ->with('success', 'NPC deleted.');
        }

        return redirect()
            ->back()
            ->with('success', 'NPC deleted.');
    }

    /**
     * used for ajaxSelect
     *
     * @param  mixed $request
     * @return void
     */
    public function search(Request $request)
    {
        $search = $request->string('q');

        return NpcType::query()
            ->select('id', 'name')
            ->when($search, function ($q) use ($search) {
                $q->where('id', $search)
                    ->orWhere('name', 'like', "%{$search}%");
            })
            ->orderBy('id')
            ->limit(50)
            ->get();
    }

    public function zones(Request $request, $id)
    {
        $npcId = (int) $id;
        try {
            $rows = DB::connection('eqemu')->table('spawn_entry as e')
                ->join('spawn2 as s', 's.spawngroupID', '=', 'e.spawngroupID')
                ->join('zone as z', 'z.short_name', '=', 's.zone')
                ->where('e.npcID', $npcId)
                ->select('z.zoneidnumber', 'z.short_name', 's.version')
                ->distinct()
                ->get();

            if ($rows->isEmpty()) {
                return response()->json([]);
            }

            $grouped = $rows->groupBy('short_name')->map(function ($items) {
                $first = $items->first();
                return [
                    'short_name' => $first->short_name,
                    'zoneidnumber' => (int) $first->zoneidnumber,
                    'versions' => $items->pluck('version')->unique()->values()->all(),
                ];
            })->values();

            return response()->json($grouped);
        } catch (\Throwable $e) {
            Log::error('NpcTypeController::zones error', ['id' => $npcId, 'error' => $e->getMessage()]);
            return response()->json(['error' => 'Unable to resolve zones for this NPC'], 500);
        }
    }

    public function zoneVersions(Request $request, $zoneid)
    {
        $zoneIdNum = (int) $zoneid;
        // Return only positive version numbers; 0 is the default and shouldn't be listed
        $versions = Zone::where('zoneidnumber', $zoneIdNum)
            ->where('version', '>', 0)
            ->orderBy('version')
            ->pluck('version')
            ->unique()
            ->values()
            ->all();

        return response()->json($versions);
    }

    /**
     * updates npc's primary faction
     *
     * @param  mixed $request
     * @param  mixed $npc
     * @return void
     */
    public function updateFaction(Request $request, NpcType $npc)
    {
        $request->validate([
            'npc_faction_id' => 'integer|min:0|max:2147483647|nullable'
        ]);

        $npc->update([
            'npc_faction_id' => $request->npc_faction_id
        ]);

        toast()->success('Saved!', 'NPC Faction updated successfully');

        return response()->json(['success' => true]);
    }

    /**
     * updates npc's loottable_id
     *
     * @param  mixed $request
     * @param  mixed $npc
     * @return void
     */
    public function updateLoottable(Request $request, NpcType $npc)
    {
        $request->validate([
            'loottable_id' => 'integer|min:0|max:2147483647|nullable'
        ]);

        $npc->update([
            'loottable_id' => $request->loottable_id
        ]);

        toast()->success('Saved!', 'NPC Loot Table updated successfully');

        return response()->json(['success' => true]);
    }

    /**
     * updates npc's spell set
     *
     * @param  mixed $request
     * @param  mixed $npc
     * @return void
     */
    public function updateSpellset(Request $request, NpcType $npc)
    {
        $request->validate([
            'npc_spells_id' => 'integer|min:0|max:2147483647|nullable'
        ]);

        $npc->update([
            'npc_spells_id' => $request->npc_spells_id
        ]);

        toast()->success('Saved!', 'NPC Spell Set updated successfully');

        return response()->json(['success' => true]);
    }

    public function races()
    {
        return response()->json(config('everquest.db_races'));
    }

    public function race($id)
    {
        $r = config('everquest.db_races');
        return response()->json([
            'id' => $id,
            'label' => $r[$id] ?? null,
        ]);
    }
}
