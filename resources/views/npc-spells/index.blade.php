@extends('layouts.app')
@section('title', 'NPC Spells')
@section('page-title', 'NPC Spells')

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">

        <x-top-links>
            <button type="button" class="btn btn-soft btn-success"
                @click="$store.modalForm.openCreate({
                baseUrl: '{{ route('npc-spells.store') }}',
                resourceName: 'NPC Spellset',
                defaults: {

                }
            })">
                <x-ui.icon name="add" /> New NPC Spell Set
            </button>
        </x-top-links>

        <x-ui.table>
            <x-slot:head>
                <tr>
                    <th scope="col">Name</th>
                    <th scope="col" class="w-[30%]">Attack Proc</th>
                    <th scope="col" class="w-[5%]">Proc %</th>
                    <th scope="col" class="w-[5%]">Parent</th>
                    <th scope="col" class="w-[5%] text-center">Entries</th>
                    <th scope="col" class="w-[5%] text-center">NPCs</th>
                    <th scope="col" class="w-[10%] text-right">-</th>
                </tr>
            </x-slot:head>
            <x-slot:body>
                @forelse ($npcSpells as $npcSpell)
                    <tr>
                        <td>
                            <a href="{{ route('npc-spells.edit', $npcSpell) }}"
                                class="text-base link-info link-hover">
                                {{ $npcSpell->name }}
                            </a>
                        </td>
                        <td>
                            @if ($npcSpell->attackProcSpell)
                                <x-spell-link
                                    :spell_id="$npcSpell->attackProcSpell->id"
                                    :spell_name="$npcSpell->attackProcSpell->name"
                                    :spell_icon="$npcSpell->attackProcSpell->new_icon"
                                    :effects_only="1"
                                />
                            @else
                                {{ $npcSpell->attack_proc }}
                            @endif
                        </td>
                        <td>{{ $npcSpell->proc_chance }}</td>
                        <td>{{ $npcSpell->parent_list }}</td>
                        <td class="text-center">{{ $npcSpell->npc_spell_entries_count ?? 0 }}</td>
                        <td class="text-center">{{ $usageCounts[$npcSpell->id] ?? 0 }}</td>
                        <td class="text-right">
                            <div class="inline join">
                                <a href="{{ route('npc-spells.edit', $npcSpell) }}"
                                    class="join-item btn btn-sm btn-soft">
                                    <x-ui.icon name="edit" />
                                </a>
                                <form action="{{ route('npc-spells.destroy', $npcSpell) }}" method="POST"
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
                        <td colspan="7" class="text-center py-6 text-base-content/50">
                            No NPC Spells found.
                        </td>
                    </tr>
                @endforelse
            </x-slot:body>
        </x-ui.table>
    </div>

    <div class="mt-4">{{ $npcSpells->links() }}</div>

    <x-modal-form>
        @include('npc-spells.forms.new-spellset')
    </x-modal-form>
@endsection
