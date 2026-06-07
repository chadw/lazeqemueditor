<div class="space-y-6" x-data="formTracker">
    <div class="flex items-center justify-end">
        <button type="button" class="btn btn-sm btn-soft btn-success"
            @click="$store.modalForm.openCreate({
                baseUrl: '{{ route('spawngroups.store') }}',
                resourceName: 'Spawn Group',
                modal: 'spawn-group',
                defaults: { spawn_group: { name: '{{ addslashes($npc->clean_name) }} Group' } },
                meta: { onSuccess: 'attachSpawnEntryForNpc', npcId: {{ $npc->id }} }
            })">
            <x-ui.icon name="add" /> Add Spawn Group
        </button>
    </div>
    @if ($npc->spawnEntries)
        @foreach ($npc->spawnEntries as $spawn)
            <div class="card bg-base-100 shadow-sm border border-base-300 overflow-hidden"
                 x-data
                 data-spawn='@json($spawn)'>
                <div class="bg-neutral text-neutral-content px-4 py-3 flex flex-wrap justify-between items-center gap-4 border-b border-base-300">
                    <div class="flex items-center gap-3">
                        <div class="badge badge-soft badge-info font-mono text-xs">{{ $spawn->spawnGroup->name }} ({{ $spawn->spawnGroup->id }})</div>
                        <h3 class="font-bold text-sm">
                            Coordinates:
                            <span class="text-secondary font-mono ml-1">
                                @if ($spawn->spawn2)
                                    {{ floor($spawn->spawn2->x) }}, {{ floor($spawn->spawn2->y) }}, {{ floor($spawn->spawn2->z) }}
                                @else
                                    —
                                @endif
                            </span>
                        </h3>
                    </div>

                    <div class="flex gap-4 items-center text-xs uppercase tracking-wider font-bold opacity-80">
                            <div class="flex items-center gap-1">
                            <x-ui.icon name="clock" />
                            @if ($spawn->spawn2)
                                Respawn: <span class="badge badge-sm badge-soft">{{ seconds_to_human($spawn->spawn2->respawntime) }}</span>
                                @if (($spawn->spawn2->variance ?? 0) > 0)
                                    <span class="text-accent">+/- {{ seconds_to_human($spawn->spawn2->variance) }}</span>
                                @endif
                            @else
                                Respawn: <span class="badge badge-sm badge-ghost">—</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="table table-zebra table-sm w-full">
                        <thead class="bg-base-300/50">
                            <tr>
                                <th scope="col" class="w-[5%]">NPC ID</th>
                                <th scope="col">NPCs</th>
                                <th scope="col" class="w-[10%] text-center">Chance</th>
                                <th scope="col" class="w-[10%] text-right">-</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($spawn->spawn2 && $spawn->spawn2->npcs)
                                @foreach ($spawn->spawn2->npcs as $phs)
                                    <tr class="{{ $npc->id == $phs->id ? 'bg-info/10 font-bold' : '' }}"
                                        x-data
                                        data-entry='@json($phs)'
                                    >
                                        <td class="font-mono text-xs text-neutral-400">{{ $phs->id }}</td>
                                        <td>
                                            @if ($npc->id == $phs->id)
                                                <span class="text-info inline-flex"><x-ui.icon name="corner-down-right" /></span> {{ $npc->clean_name }}
                                                <span class="badge badge-sm badge-ghost ml-2">Current</span>
                                            @else
                                                <a href="{{ route('npcs.edit', array_merge(request()->query(), ['npc' => $phs->id])) }}"
                                                class="link link-info link-hover">
                                                    {{ $phs->clean_name }}
                                                </a>
                                            @endif
                                        </td>
                                        @php
                                            // Look up the specific SpawnEntry for this NPC within the group
                                            $groupEntry = null;
                                            if (isset($spawn->spawnGroup) && isset($spawn->spawnGroup->spawnentries)) {
                                                $groupEntry = $spawn->spawnGroup->spawnentries->firstWhere('npcID', $phs->id);
                                            }
                                            $displayChance = $groupEntry->chance ?? $spawn->chance ?? null;
                                        @endphp
                                        <td class="text-center opacity-70">{{ $displayChance !== null ? $displayChance . '%' : '—' }}</td>
                                        <td class="text-right">
                                            <div class="join">
                                                <button type="button" class="btn btn-xs btn-soft join-item"
                                                    @click="$store.modalForm.openEdit(
                                                        {
                                                            spawn: JSON.parse($el.closest('div.card').dataset.spawn || '{}'),
                                                            entry: JSON.parse($el.closest('tr').dataset.entry || '{}')
                                                        },
                                                        '{{ route('spawngroups.entries.update', [
                                                            'spawngroup' => $spawn->spawnGroup->id,
                                                            'npcID' => $spawn->npcID,
                                                        ]) }}',
                                                        {
                                                            modal: 'spawn-entry',
                                                            resourceName: 'Edit Entry - {{ $phs->clean_name }} ({{ $phs->id }})'
                                                        }
                                                    )">
                                                    <x-ui.icon name="edit" />
                                                </button>
                                                <form method="POST"
                                                    action="{{ route('spawngroups.entries.destroy', [
                                                        'spawngroup' => $spawn->spawnGroup->id,
                                                        'npcID' => $spawn->npcID,
                                                    ]) }}"
                                                    onsubmit="return confirm('Delete Spawn?')">
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
                            @endif
                        </tbody>
                    </table>
                </div>

                <div class="card-actions justify-between p-2 bg-base-300/20 border-t border-base-300">
                    <button type="button" class="btn btn-xs btn-soft gap-2"
                        @click="$store.modalForm.openEdit(
                            {{ $spawn->toJson() }},
                            '{{ route('spawngroups.update', $spawn->spawnGroup->id) }}',
                            {
                                modal: 'spawn-group',
                                resourceName: 'Edit Spawn Group - {{ $spawn->spawnGroup->name }} ({{ $spawn->spawnGroup->id }})'
                            }
                        )">
                        <x-ui.icon name="settings" /> Spawngroup Settings
                    </button>
                    <div class="flex gap-2 items-center">
                        <button type="button" class="btn btn-xs btn-soft btn-success"
                            @click="$store.modalForm.openCreate({
                            baseUrl: '{{ route('spawngroups.entries.store', $spawn->spawnGroup->id) }}',
                            resourceName: 'Add NPC Spawn Entry',
                            modal: 'spawn-entry',
                            defaults: {
                                spawn: {
                                    chance: 0,
                                    condition_value_filter: 1,
                                    min_time: 0,
                                    max_time: 0,
                                }
                            }
                        })">
                            <x-ui.icon name="add" /> Add NPC
                        </button>
                        @php
                            $spZone = optional($spawn->spawn2)->zone ?? optional($spawn->spawnGroup->spawn2->first())->zone ?? 'unknown';
                        @endphp
                        <button type="button" class="btn btn-xs btn-soft btn-success"
                            @click="$store.modalForm.openCreate({
                            baseUrl: '{{ route("spawngroups.spawnpoints.store", $spawn->spawnGroup->id) }}',
                            resourceName: 'New Spawnpoint',
                            modal: 'spawn-point',
                            defaults: {
                                spawngroupID: {{ $spawn->spawnGroup->id }},
                                zone: '{{ $spZone }}',
                                version: 0,
                                x: 0.000000,
                                y: 0.000000,
                                z: 0.000000,
                                heading: 0.000000,
                                respawntime: 0,
                                variance: 0,
                                pathgrid: 0,
                                path_when_zone_idle: 0,
                                _condition: 0,
                                cond_value: 1,
                            }
                        })">
                            <x-ui.icon name="add" /> Add Spawnpoint
                        </button>
                        <form method="POST" action="{{ route('spawngroups.destroy', $spawn->spawnGroup->id) }}" onsubmit="return confirm('Delete Spawngroup?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-xs btn-soft btn-error">Delete Group</button>
                        </form>
                    </div>
                </div>

                {{-- start: spawn points --}}
                @if ($spawn->spawnGroup->spawn2->isNotEmpty())
                    <div class="join join-vertical bg-neutral/50 p-2 rounded">
                    @foreach ($spawn->spawnGroup->spawn2 as $k => $sp)
                        <details class="collapse collapse-arrow join-item border border-base-300 bg-base-100"
                            name="npc-sp-{{ $k }}"
                            x-data
                            data-spawnpt='@json($sp)'
                        >
                            <summary class="collapse-title font-semibold flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 p-2 pe-12">
                                <div class="flex items-center gap-3">
                                    <span class="font-mono text-xs text-neutral-400">#{{ $sp->id }}</span>
                                    <span class="capitalize">{{ $sp->zone }}</span>
                                    <span class="text-xs text-neutral-400">v{{ $sp->version }}</span>
                                </div>
                                <div class="text-xs text-neutral-400 flex items-center gap-3 font-mono">
                                    <span>{{ floor($sp->x) }}, {{ floor($sp->y) }}, {{ floor($sp->z) }}</span>
                                    <span>Heading: {{ (int) $sp->heading }}</span>
                                    <span class="badge badge-sm badge-ghost">Respawn: {{ seconds_to_human($sp->respawntime) }}@if($sp->variance>0) +/- {{ seconds_to_human($sp->variance) }}@endif</span>
                                </div>
                            </summary>

                            <div class="collapse-content text-sm">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                                    <div class="space-y-1">
                                        <div class="flex justify-between"><span class="text-neutral-400">Path Grid</span><span class="font-mono">{{ $sp->pathgrid }}</span></div>
                                        <div class="flex justify-between"><span class="text-neutral-400">Path When Idle</span><span class="font-mono">{{ $sp->path_when_zone_idle }}</span></div>
                                        <div class="flex justify-between"><span class="text-neutral-400">Condition</span><span class="font-mono">{{ $sp->_condition }} ({{ $sp->cond_value }})</span></div>
                                        <div class="flex justify-between"><span class="text-neutral-400">Animation</span><span class="font-mono">{{ $sp->animation }}</span></div>
                                    </div>
                                    <div class="space-y-1">
                                        <div class="flex justify-between"><span class="text-neutral-400">Min Expansion</span><span class="font-mono">{{ $sp->min_expansion }}</span></div>
                                        <div class="flex justify-between"><span class="text-neutral-400">Max Expansion</span><span class="font-mono">{{ $sp->max_expansion }}</span></div>
                                        <div class="flex justify-between"><span class="text-neutral-400">Content Flags</span><span class="font-mono">{{ $sp->content_flags ?? '—' }}</span></div>
                                        <div class="flex justify-between"><span class="text-neutral-400">Disabled Flags</span><span class="font-mono">{{ $sp->content_flags_disabled ?? '—' }}</span></div>
                                    </div>
                                </div>

                                <div class="mt-3 flex items-center gap-2">
                                    <button type="button" class="btn btn-xs btn-soft gap-2"
                                        @click="$store.modalForm.openEdit(
                                            $el.closest('details').dataset.spawnpt,
                                            '{{ route('spawngroups.spawnpoints.update', ['spawngroup' => $sp->spawngroupID, 'spawnpoint' => $sp->id]) }}',
                                            {
                                                modal: 'spawn-point',
                                                resourceName: 'Edit Spawnpoint - {{ $sp->zone }} v{{ $sp->version }} ({{ $sp->id }})'
                                            }
                                        )">
                                        <x-ui.icon name="edit" /> Edit Spawnpoint
                                    </button>

                                    <form method="POST" action="{{ route('spawngroups.spawnpoints.destroy', ['spawngroup' => $sp->spawngroupID, 'spawnpoint' => $sp->id]) }}" onsubmit="return confirm('Delete spawnpoint?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-xs btn-soft btn-error"><x-ui.icon name="delete" /> Delete Spawnpoint</button>
                                    </form>
                                    <div class="ml-auto text-xs text-neutral-400">Raw ID: <span class="font-mono">{{ $sp->id }}</span></div>
                                </div>
                            </div>
                        </details>
                    @endforeach
                    </div>
                @endif
                {{-- end: spawn points --}}
            </div>
        @endforeach
    @else
        <div class="alert alert-warning">
            <x-ui.icon name="warning" size="w-6 h-6" />
            <span>This NPC does not have any spawn entries assigned.</span>
        </div>
    @endif
</div>

<script>
    async function attachSpawnEntryForNpc(data) {
        try {
            const store = window.Alpine?.store ? window.Alpine.store('modalForm') : null;
            const npcId = store && store.meta ? store.meta.npcId : null;
            const groupId = data?.id ?? data?.data?.id ?? null;
            if (!groupId || !npcId) {
                window.location.reload();
                return;
            }

            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const fd = new FormData();
            fd.append('npcID', String(npcId));

            const entriesUrl = `/spawngroups/${groupId}/entries`;
            const res = await fetch(entriesUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest' },
                body: fd,
                credentials: 'same-origin'
            });

            if (!res.ok) {
                console.error('Failed attaching NPC to spawn group', await res.text().catch(() => res.status));
                window.location.reload();
                return;
            }

            window.location.reload();
        } catch (e) {
            console.error(e);
            window.location.reload();
        }
    }
</script>
