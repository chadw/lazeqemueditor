<div class="space-y-6 mt-6" x-data="formTracker" data-spawngroup='@json($spawngroup)'>
    {{-- Spawn Entries --}}
    <div class="card bg-base-100 shadow-sm border border-base-300 overflow-hidden">
        <div class="bg-neutral text-neutral-content px-4 py-3 flex items-center justify-between">
            <h3 class="font-bold">Spawn Entries</h3>
            <div class="flex items-center gap-2">
                <button type="button" class="btn btn-sm btn-soft btn-success"
                    @click="$store.modalForm.openCreate({
                        baseUrl: '{{ route('spawngroups.entries.store', $spawngroup->id) }}',
                        resourceName: 'Add NPC Spawn Entry',
                        modal: 'spawn-entry',
                        defaults: {
                            chance: 0,
                            condition_value_filter: 1,
                            min_time: 0,
                            max_time: 0
                        }
                    })">
                    <x-ui.icon name="add" /> Add Entry
                </button>
            </div>
        </div>
        <div class="overflow-x-auto">
            @if ($spawngroup->spawnentries && $spawngroup->spawnentries->isNotEmpty())
                <table class="table table-zebra table-sm w-full">
                    <thead class="bg-base-300/50">
                        <tr>
                            <th class="w-[5%]">NPC ID</th>
                            <th>NPC</th>
                            <th class="w-[10%] text-center">Chance</th>
                            <th class="w-[10%] text-right">-</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($spawngroup->spawnentries as $entry)
                            <tr x-data data-entry='@json($entry)'
                                class="{{ isset($entry->npc) && $entry->npc->id == request('highlight') ? 'bg-info/10 font-bold' : '' }}">
                                <td class="font-mono text-xs text-neutral-400">{{ $entry->npcID }}</td>
                                <td>
                                    @if (isset($entry->npc))
                                        <a href="{{ route('npcs.edit', ['npc' => $entry->npc->id]) }}"
                                            class="link link-info link-hover">{{ $entry->npc->clean_name }}</a>
                                    @else
                                        Unknown ({{ $entry->npcID }})
                                    @endif
                                </td>
                                <td class="text-center opacity-70">{{ $entry->chance ?? '—' }}%</td>
                                <td class="text-right">
                                    <div class="join">
                                        <button type="button" class="btn btn-xs btn-soft join-item"
                                            @click="$store.modalForm.openEdit(
                                                $el.closest('tr').dataset.entry,
                                                '{{ route('spawngroups.entries.update', [
                                                    'spawngroup' => $spawngroup->id,
                                                    'npcID' => $entry->npcID,
                                                ]) }}',
                                                {
                                                    modal: 'spawn-entry',
                                                    resourceName: 'Edit Spawn Entry'
                                                }
                                            )">
                                            <x-ui.icon name="edit" />
                                        </button>

                                        <form method="POST"
                                            action="{{ route('spawngroups.entries.destroy', ['spawngroup' => $spawngroup->id, 'npcID' => $entry->npcID]) }}"
                                            onsubmit="return confirm('Delete Spawn Entry?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-xs btn-soft btn-error join-item"><x-ui.icon
                                                    name="delete" /></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="p-4 text-sm text-neutral-500">No spawn entries for this group yet.</div>
            @endif
        </div>
    </div>

    <div class="card bg-base-100 shadow-sm border border-base-300 overflow-hidden">
        <div class="bg-neutral text-neutral-content px-4 py-3 flex items-center justify-between">
            <h3 class="font-bold">Spawn Points</h3>
            <div class="flex items-center gap-2">
                <button type="button" class="btn btn-sm btn-soft btn-success"
                    @click="$store.modalForm.openCreate({
                        baseUrl: '{{ route('spawngroups.spawnpoints.store', $spawngroup->id) }}',
                        resourceName: 'New Spawnpoint',
                        modal: 'spawn-point',
                        defaults: {
                            spawngroupID: {{ $spawngroup->id }},
                            zone: '{{ $spawngroup->spawn2->first()->zone ?? 'unknown' }}',
                            version: 0, x: 0.000000, y: 0.000000, z: 0.000000, heading: 0.000000,
                            respawntime: 0, variance: 0, pathgrid: 0, path_when_zone_idle: 0, _condition: 0, cond_value: 1
                        }
                    })">
                    <x-ui.icon name="add" /> Add Spawnpoint
                </button>
            </div>
        </div>

        @if ($spawngroup->spawn2 && $spawngroup->spawn2->isNotEmpty())
            <table class="table table-zebra table-sm w-full">
                <thead class="bg-base-300/50">
                    <tr>
                        <th class="w-[5%]">ID</th>
                        <th>Zone/Version</th>
                        <th class="w-[20%]">X,Y,Z</th>
                        <th class="w-[5%]">Respawn</th>
                        <th class="w-[10%] text-right">-</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($spawngroup->spawn2 as $k => $sp)
                        <tr x-data data-spawnpt='@json($sp)'>
                            <td class="font-mono text-xs text-neutral-400">{{ $sp->id }}</td>
                            <td>{{ $sp->zone }} v{{ $sp->version }}</td>
                            <td class="text-xs">
                                <span class="badge badge-sm badge-soft badge-accent">
                                    {{ floor($sp->x) }}, {{ floor($sp->y) }}, {{ floor($sp->z) }} - Heading: {{ (int) $sp->heading }}
                                </span>
                            </td>
                            <td>
                                {{ seconds_to_human($sp->respawntime) }}
                                @if ($sp->variance > 0)
                                    +/- {{ seconds_to_human($sp->variance) }}
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="join">
                                    <button type="button" class="btn btn-xs btn-soft join-item"
                                        @click="$store.modalForm.openEdit(
                                        $el.closest('tr').dataset.spawnpt,
                                        '{{ route('spawngroups.spawnpoints.update', ['spawngroup' => $spawngroup->id, 'spawnpoint' => $sp->id]) }}',
                                        {
                                            modal: 'spawn-point',
                                            resourceName: 'Edit Spawnpoint'
                                        }
                                    )">
                                        <x-ui.icon name="edit" />
                                    </button>
                                    <form method="POST"
                                        action="{{ route('spawngroups.spawnpoints.destroy', [
                                            'spawngroup' => $spawngroup->id,
                                            'spawnpoint' => $sp->id,
                                        ]) }}"
                                        onsubmit="return confirm('Delete spawnpoint?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-xs btn-soft btn-error join-item">
                                            <x-ui.icon name="delete" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="p-4 text-sm text-neutral-500">No spawn points for this group yet.</div>
        @endif
    </div>
</div>
