<div class="space-y-6" x-data="formTracker">
    @if ($npc->primaryFaction && $factionUsage > 0)
        <x-ui.alert-info>
            <p class="font-bold">Warning: This NPC's primary faction is shared with {{ $factionUsage }} other NPC(s).</p>
            <p>Editing the faction will affect all NPCs that use it. Consider creating a new npc faction if you want to make unique changes.</p>
        </x-ui.alert-info>
    @endif

    {{-- primary faction --}}
    @if ($npc->primaryFaction)
        <div class="card bg-neutral text-neutral-content shadow-sm">
            <div class="card-body p-4 flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div>
                        <h2 class="card-title text-sm opacity-70 uppercase tracking-widest">
                            Primary Faction - {{ $npc->primaryFaction->name }}
                            <span class="ml-1 text-xs font-mono normal-case">(ID: {{ $npc->primaryFaction->id }})</span>
                        </h2>
                        <p class="text-xl font-bold">
                            {{ $npc->primaryFaction->faction->name ?? 'Unknown' }}
                            <span class="ml-1 text-sm font-mono">(ID: {{ $npc->npc_faction_id }})</span>
                        </p>
                    </div>
                </div>
                <div class="flex flex-col md:flex-row items-center gap-2 w-full md:w-auto">
                    <div class="w-full md:w-96">
                        @include('npcs.partials.faction-selector', [
                            'prefill' => [
                                'id' => $npc->primaryFaction->id,
                                'name' => $npc->primaryFaction->name,
                            ],
                        ])
                    </div>
                    <div class="divider divider-warning md:divider-horizontal text-xs text-neutral-200 h-8 md:h-12">OR</div>
                    <button type="button" class="btn btn-soft btn-success w-full md:w-auto"
                        @click="$store.modalForm.openCreate({
                        baseUrl: '{{ route('npcs.primary-factions.store') }}',
                        resourceName: 'NPC Primary Faction',
                        modal: 'primary-faction',
                        defaults: {
                            npc_id: {{ $npc->id }},
                            name: '{{ $npc->name }}_Faction',
                            primaryfaction: 0,
                        }
                    })">
                        <x-ui.icon name="add" /> New Primary Faction
                    </button>
                </div>
            </div>
        </div>
    @else
        <x-ui.alert-warning>
            <div class="flex flex-col md:flex-row items-center justify-between w-full gap-4">
                <label class="font-medium shrink-0">This NPC does not have a primary faction set.</label>
                <div class="flex flex-col md:flex-row items-center gap-2 w-full md:w-auto">
                    <div class="w-full md:w-96">
                        @include('npcs.partials.faction-selector', ['prefill' => 0])
                    </div>
                    <div class="divider divider-info md:divider-horizontal text-xs text-neutral-200 h-8 md:h-12">OR</div>
                    <button type="button" class="btn btn-soft btn-success float-end"
                        @click="$store.modalForm.openCreate({
                        baseUrl: '{{ route('npcs.primary-factions.store') }}',
                        resourceName: 'NPC Primary Faction',
                        modal: 'primary-faction',
                        defaults: {
                            npc_id: {{ $npc->id }},
                            name: '{{ $npc->name }}_Faction',
                            primaryfaction: 0,
                        }
                    })">
                        <x-ui.icon name="add" /> New Primary Faction
                    </button>
                </div>
            </div>
        </x-ui.alert-warning>
    @endif

    {{-- faction entries --}}
    <div class="card bg-base-100 shadow border border-base-300">
        <div class="border border-base-content/5 overflow-x-auto">
            <table class="table table-auto table-zebra md:table-fixed w-full">
                <thead class="text-xs uppercase bg-neutral">
                    <tr class="bg-base-300">
                        <th>Faction Name</th>
                        <th class="text-center">Value</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($npc->factionEntries as $entry)
                        <tr class="hover">
                            <td>
                                <div class="font-bold">{{ $entry['faction']['name'] }}</div>
                                <div class="text-xs opacity-50">ID: {{ $entry['faction_id'] }}</div>
                            </td>
                            <td class="text-center">
                                @if ($entry['value'] > 0)
                                    <div class="badge badge-soft badge-success gap-1 font-mono">
                                        +{{ $entry['value'] }}
                                    </div>
                                @elseif($entry['value'] < 0)
                                    <div class="badge badge-soft badge-error gap-1 font-mono">
                                        {{ $entry['value'] }}
                                    </div>
                                @else
                                    <div class="badge badge-soft font-mono">0</div>
                                @endif
                            </td>
                            <td class="text-right">
                                <button class="btn btn-ghost btn-xs btn-square">
                                    <x-ui.icon name="edit" />
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
