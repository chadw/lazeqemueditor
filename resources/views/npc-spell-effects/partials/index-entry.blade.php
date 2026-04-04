<div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">
    <div class="card bg-base-100 card-sm shadow-sm">
        <div class="card-body">
            <h2 class="card-title flex items-center">
                NPC Spell Effect Entries
                <button type="button" class="btn btn-soft btn-sm btn-success ml-auto"
                    @click="$store.modalForm.openCreate({
                        baseUrl: '{{ route('npc-spell-effect-entry.store') }}',
                        resourceName: 'NPC Spell Effect Entry',
                        defaults: {
                            npc_spells_effects_id: {{ $npcSpellEffect->id }}
                        }
                    })">
                    <x-ui.icon name="add" /> New NPC Spell Effect Entry
                </button>
            </h2>
            <div class="card bg-base-100 shadow">
                <div class="border border-base-content/5 overflow-x-auto">
                    <table class="table table-auto table-zebra md:table-fixed w-full">
                        <thead class="text-xs uppercase bg-neutral">
                            <tr>
                                <th scope="col" class="w-[30%]">Effect</th>
                                <th scope="col">Min Level</th>
                                <th scope="col">Max Level</th>
                                <th scope="col">SE Base</th>
                                <th scope="col">SE Limit</th>
                                <th scope="col">SE Max</th>
                                <th scope="col" class="w-[10%] text-right">-</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($npcSpellEffect->npcSpellEffectEntries as $entry)
                                <tr x-data data-ncse='@json($entry)'>
                                    <td>{{ config('everquest.spell_effects')[$entry->spell_effect_id] }}</td>
                                    <td>{{ $entry->minlevel }}</td>
                                    <td>{{ $entry->maxlevel }}</td>
                                    <td>{{ $entry->se_base }}</td>
                                    <td>{{ $entry->se_limit }}</td>
                                    <td>{{ $entry->se_max }}</td>
                                    <td class="text-right">
                                        <div class="inline join">
                                            <button type="button" class="join-item btn btn-sm btn-soft"
                                                @click="$store.modalForm.openEdit(
                                                    $el.closest('tr').dataset.ncse,
                                                    '{{ route('npc-spell-effect-entry.update', $entry) }}',
                                                    { resourceName: 'Edit NPC Spell Effect Entry'
                                                })">
                                                <x-ui.icon name="edit" />
                                            </button>
                                            <form action="{{ route('npc-spell-effect-entry.destroy', $entry) }}"
                                                method="POST" class="inline">
                                                @csrf @method('DELETE')
                                                <button class="join-item btn btn-sm btn-soft btn-error"
                                                    onclick="return confirm('Delete?')">
                                                    <x-ui.icon name="delete" />
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <x-modal-form>
        @include('npc-spell-effects.forms.form-entry')
    </x-modal-form>
</div>
