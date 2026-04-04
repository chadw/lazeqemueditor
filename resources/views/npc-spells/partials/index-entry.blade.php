<div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">
    <x-ui.card>
        <x-slot:header>
            <h3 class="card-title text-lg">
                NPC Spell Effect Entries
                @if ($modalScope === 'parent-set')
                    - Parent Set {{ $npcSpell->id }}: {{ $npcSpell->name }}
                @endif
            </h3>
            <button type="button" class="btn btn-sm btn-soft btn-success ml-auto"
                @click="$store.modalForm.openCreate({
                    baseUrl: '{{ route('npc-spell-entry.store') }}',
                    resourceName: 'NPC Spell Entry',
                    defaults: {
                        npc_spells_id: {{ $npcSpell->id }},
                        spellid: 0,
                        type: 1,
                        minlevel: 1,
                        maxlevel: 255,
                        manacost: -1,
                        recast_delay: -1,
                        priority: 0,
                        min_hp: 0,
                        max_hp: 0,
                    },
                    modal: '{{ $modalScope }}',
                })">
                <x-ui.icon name="add" /> New NPC Spell Entry
            </button>
        </x-slot:header>
        <div>
            <table class="table table-auto table-zebra md:table-fixed w-full table-pin-rows">
                <thead class="text-sm uppercase bg-base-300">
                    <tr>
                        <th scope="col" class="w-[30%]">Spell</th>
                        <th scope="col">Type</th>
                        <th scope="col">Min Level</th>
                        <th scope="col">Max Level</th>
                        <th scope="col">Mana</th>
                        <th scope="col">Recast</th>
                        <th scope="col">Priority</th>
                        <th scope="col">Flags</th>
                        <th scope="col" class="w-[10%] text-right">-</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($npcSpell->npcSpellEntries as $entry)
                        <tr x-data data-ncse='@json($entry)'>
                            <td>
                                @if ($entry->spells)
                                    <x-spell-link
                                        :spell_id="$entry->spells->id"
                                        :spell_name="$entry->spells->name"
                                        :spell_icon="$entry->spells->new_icon"
                                        spell_class="flex"
                                        :effects_only="1"
                                    />
                                @else
                                    <span class="italic opacity-60">Spell #{{ $entry->spellid }}</span>
                                @endif
                            </td>
                            <td>{{ config('everquest.spell_types')[$entry->type] ?? $entry->type }}</td>
                            <td>{{ $entry->minlevel }}</td>
                            <td>{{ $entry->maxlevel }}</td>
                            <td>{{ $entry->manacost != -1 ? $entry->manacost : 'Default' }}</td>
                            <td>{{ $entry->recast_delay != -1 ? $entry->recast_delay : 'Default' }}</td>
                            <td>{{ $entry->priority }}</td>
                            <td>{{ $entry->has_content_restrictions ? 'Yes' : 'No' }}</td>
                            <td class="text-right">
                                <div class="inline join">
                                    <button type="button" class="join-item btn btn-sm btn-soft"
                                        @click="$store.modalForm.openEdit(
                                            $el.closest('tr').dataset.ncse,
                                            '{{ route('npc-spell-entry.update', $entry) }}',
                                            {
                                                modal: '{{ $modalScope }}',
                                                resourceName: 'Edit NPC Spell Entry'
                                            }
                                        )">
                                        <x-ui.icon name="edit" />
                                    </button>
                                    <form action="{{ route('npc-spell-entry.destroy', $entry) }}" method="POST"
                                        class="inline">
                                        @csrf @method('DELETE')
                                        <button class="join-item btn btn-sm btn-soft btn-error"
                                            onclick="return confirm('Delete?')">
                                            <x-ui.icon name="delete" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-6 text-base-content/50">
                                No entries for this spell set.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-slot:footer>
            <div></div>
            <button type="button" class="btn btn-sm btn-soft btn-success ml-auto"
                @click="$store.modalForm.openCreate({
                    baseUrl: '{{ route('npc-spell-entry.store') }}',
                    resourceName: 'NPC Spell Entry',
                    defaults: {
                        npc_spells_id: {{ $npcSpell->id }},
                        spellid: 0,
                        type: 1,
                        minlevel: 1,
                        maxlevel: 255,
                        manacost: -1,
                        recast_delay: -1,
                        priority: 0,
                        min_hp: 0,
                        max_hp: 0,
                    },
                    modal: '{{ $modalScope }}',
                })">
                <x-ui.icon name="add" /> New NPC Spell Entry
            </button>
        </x-slot:footer>
    </x-ui.card>

    <x-modal-form x-show="$store.modalForm.isOpen">
        <template x-if="$store.modalForm.activeModal === 'main-set'">
            @include('npc-spells.forms.form-entry')
        </template>
        {{-- <template x-if="$store.modalForm.activeModal === 'parent-set'">
            @include('npc-spells.forms.form-entry')
        </template> --}}
    </x-modal-form>
</div>
