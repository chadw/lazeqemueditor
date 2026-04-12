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

        @include('loot.partials.loottable-npcs', ['bg' => 'bg-base-200', 'table' => $npc->lootTable])

        <div class="card bg-base-200 border border-base-300 shadow-sm mx-auto w-full max-w-3xl">
            <div class="card-body">
                <h2 class="card-title">Loot Drop Management</h2>
                <div class="flex flex-col md:flex-row items-stretch gap-2">
                    <div class="flex-3" x-data="ajaxSelect({
                        searchUrl: '/loot/drops/search',
                        placeholder: 'Search existing Loot Drops...',
                        multiple: false,
                        required: true,
                    })" x-init="init()">
                        <label class="label"><span class="label-text font-bold">Link Existing Drop</span></label>

                        <form action="{{ route('loot.drops.link', $npc->lootTable->id) }}" method="POST"
                            class="flex join w-full">
                            @csrf
                            <input type="hidden" name="loottable_id" value="{{ $npc->lootTable->id }}">
                            <select x-ref="select" name="lootdrop_id" class="join-item w-full" required></select>
                            <button type="submit" class="btn btn-soft btn-primary join-item">Link</button>
                        </form>
                    </div>
                    <div class="divider md:divider-horizontal">OR</div>
                    <div class="flex-1 flex flex-col justify-end">
                        <button type="button" class="btn btn-soft btn-success w-full"
                            onclick="new_lootdrop_modal.showModal()">
                            <x-ui.icon name="add" /> New Loot Drop
                        </button>
                    </div>

                </div>
            </div>
        </div>
        <dialog id="new_lootdrop_modal" class="modal">
            <div class="modal-box max-w-2xl">
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>
                <h3 class="text-lg font-bold">Create New Loot Drop</h3>
                <p class="text-xs opacity-60 mb-4 italic">This drop will be automatically added to
                    "{{ $npc->lootTable->name }}"</p>

                <form action="{{ route('loot.drops.store', $npc->lootTable->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="loottable_id" value="{{ $npc->lootTable->id }}">
                    <x-form.input
                        name="name"
                        label="Drop Name"
                        placeholder="Ex: Global Rare Spells"
                        required
                    />

                    <div class="grid grid-cols-2 gap-4">
                        <x-form.input
                            name="mindrops"
                            label="Min Drops"
                            type="number"
                            value="0"
                        />
                        <x-form.input
                            name="maxdrops"
                            label="Max Drops"
                            type="number"
                            value="1"
                        />
                    </div>

                    <div class="modal-action">
                        <button type="button" class="btn" onclick="new_lootdrop_modal.close()">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create & Attach</button>
                    </div>
                </form>
            </div>
        </dialog>
        @include('loot.partials.lootdrops-edit', [
            'lt' => $npc->lootTable,
        ])
    @endif
</div>
