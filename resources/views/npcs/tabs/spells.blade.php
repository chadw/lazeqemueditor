<div class="space-y-6" x-data="formTracker">
    @if ($npc->npcSpellset && $spellsetUsage > 0)
        <x-ui.alert-info>
            <p class="font-bold">Warning: This NPC's spell set is shared with {{ $spellsetUsage }} other NPC(s).</p>
            <p>Editing the spell set will affect all NPCs that use it. Consider creating a new spell set if you want to make unique changes.</p>
        </x-ui.alert-info>
    @endif
    {{-- npc spells --}}
    @if ($npc->npcSpellset)
        <div class="card bg-neutral text-neutral-content shadow-sm">
            <div class="card-body p-4 flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div>
                        <h2 class="card-title text-sm opacity-70 uppercase tracking-widest">
                            NPC Spell Set
                        </h2>
                        <p class="text-xl font-bold">
                            {{ $npc->npcSpellset->name ?? 'Unknown' }}
                            <span class="ml-1 text-sm font-mono">(ID: {{ $npc->npcSpellset->id }})</span>
                        </p>
                    </div>
                </div>
                <div class="flex flex-col md:flex-row items-center gap-2 w-full md:w-auto">
                    <div class="w-full md:w-96">
                        @include('npcs.partials.npcspell-selector', [
                            'prefill' => [
                                'id' => $npc->npcSpellset->id,
                                'name' => $npc->npcSpellset->name,
                            ],
                        ])
                    </div>
                    <div class="divider divider-warning md:divider-horizontal text-xs text-neutral-200 h-8 md:h-12">OR</div>
                    <button type="button" class="btn btn-soft btn-success w-full md:w-auto"
                        @click="$store.modalForm.openCreate({
                        baseUrl: '{{ route('npc-spells.store') }}',
                        resourceName: 'NPC Spell Set',
                        modal: 'new-set',
                        defaults: {
                            npc_id: {{ $npc->id }},
                            attack_proc: -1,
                            proc_chance: 3,
                            range_proc: -1,
                            rproc_chance: 0,
                            defensive_proc: -1,
                            dproc_chance: 0,
                            fail_recast: 0,
                        }
                    })">
                        <x-ui.icon name="add" /> New NPC Spell Set
                    </button>
                </div>
            </div>
        </div>

        @include('npc-spells.partials.index-entry', [
            'npcSpell' => $npc?->npcSpellset,
            'modalScope' => 'main-set'
        ])

        @if ($npc->npcSpellset->parentSet)
            @include('npc-spells.partials.index-entry', [
                'npcSpell' => $npc->npcSpellset->parentSet,
                'modalScope' => 'main-set'
            ])
        @endif
    @else
        <x-ui.alert-warning>
            <div class="flex flex-col md:flex-row items-center justify-between w-full gap-4">
                <label class="font-medium shrink-0">This NPC does not have a spell set</label>
                <div class="flex flex-col md:flex-row items-center gap-2 w-full md:w-auto">
                    <span class="text-white/80">Attach existing NPC Spell Set</span>
                    <div class="w-full md:w-96">
                        @include('npcs.partials.npcspell-selector', ['prefill' => 0])
                    </div>
                    <div class="divider divider-info md:divider-horizontal text-xs text-neutral-200 h-8 md:h-12">OR</div>
                    <button type="button" class="btn btn-soft btn-success float-end"
                        @click="$store.modalForm.openCreate({
                        baseUrl: '{{ route('npc-spells.store') }}',
                        resourceName: 'NPC Spell Set',
                        modal: 'new-set',
                        defaults: {
                            npc_id: {{ $npc->id }},
                            attack_proc: -1,
                            proc_chance: 3,
                            range_proc: -1,
                            rproc_chance: 0,
                            defensive_proc: -1,
                            dproc_chance: 0,
                            fail_recast: 0,
                        }
                    })">
                        <x-ui.icon name="add" /> New NPC Spell Set
                    </button>
                </div>
            </div>
        </x-ui.alert-warning>
    @endif
</div>
