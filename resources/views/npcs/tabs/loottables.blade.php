<div class="space-y-6" x-data="formTracker">
    @if ($npc->lootTable && $loottableUsage > 0)
        <x-ui.alert-info>
            <p class="font-bold">Warning: This NPC's loot table is shared with {{ $loottableUsage }} other NPC(s).</p>
            <p>Editing the loot table will affect all NPCs that use it. Consider creating a new loot table if you want to make unique changes.</p>
        </x-ui.alert-info>
    @endif

    {{-- loottable association --}}
    @if ($npc->lootTable)
        <div class="card bg-neutral text-neutral-content shadow-sm">
            <div class="card-body p-4 flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div>
                        <h2 class="card-title text-sm opacity-70 uppercase tracking-widest">
                            Attach Loot Table
                        </h2>
                        <p class="text-xl font-bold">
                            {{ $npc->lootTable->name ?? 'Unknown' }}
                            <span class="ml-1 text-sm font-mono">(ID: {{ $npc->lootTable->id }})</span>
                        </p>
                    </div>
                </div>
                <div class="flex flex-col md:flex-row items-center gap-2 w-full md:w-auto">
                    <div class="w-full md:w-96">
                        @include('npcs.partials.loottable-selector', [
                            'prefill' => [
                                'id' => $npc->lootTable->id,
                                'name' => $npc->lootTable->name,
                            ],
                        ])
                    </div>
                    <div class="divider divider-warning md:divider-horizontal text-xs text-neutral-200 h-8 md:h-12">OR</div>
                    <button type="button" class="btn btn-soft btn-success w-full md:w-auto"
                        @click="$store.modalForm.openCreate({
                        baseUrl: '{{ route('loot.store') }}',
                        resourceName: 'NPC Loot Table',
                        modal: 'new-loottable',
                        defaults: {
                            npc_id: {{ $npc->id }},
                            name: '{{ $npc->name }}_Loot',
                        }
                    })">
                        <x-ui.icon name="add" /> New Loot Table
                    </button>
                </div>
            </div>
        </div>
    @else
        <x-ui.alert-warning>
            <div class="flex flex-col md:flex-row items-center justify-between w-full gap-4">
                <label class="font-medium shrink-0">This NPC does not have a loot table assigned.</label>
                <div class="flex flex-col md:flex-row items-center gap-2 w-full md:w-auto">
                    <div class="w-full md:w-96">
                        @include('npcs.partials.loottable-selector', ['prefill' => 0])
                    </div>
                    <div class="divider divider-info md:divider-horizontal text-xs text-neutral-200 h-8 md:h-12">OR</div>
                    <button type="button" class="btn btn-soft btn-success float-end"
                        @click="$store.modalForm.openCreate({
                        baseUrl: '{{ route('loot.store') }}',
                        resourceName: 'NPC Loot Table',
                        modal: 'new-loottable',
                        defaults: {
                            npc_id: {{ $npc->id }},
                            name: '{{ $npc->name }}_Loot',
                        }
                    })">
                        <x-ui.icon name="add" /> New Loot Table
                    </button>
                </div>
            </div>
        </x-ui.alert-warning>
    @endif

    @if ($npc->lootTable)
        @include('loot.partials.loottable-edit', [
            'lt' => $npc->lootTable,
        ])

        @include('loot.partials.lootdrops-edit', [
            'lt' => $npc->lootTable,
        ])
    @endif
</div>
