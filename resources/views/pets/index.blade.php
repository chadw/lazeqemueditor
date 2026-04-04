@extends('layouts.app')
@section('title', 'Pets')
@section('page-title', 'Pets')

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">

        <x-top-links>
            <a href="{{ route('pets.equipment.index') }}" class="btn btn-soft btn-accent">
                Pet Equipment
            </a>
            <a href="{{ route('beastlord-pets.index') }}" class="btn btn-soft btn-accent">
                Beastlord Pets
            </a>
            <button type="button" class="btn btn-soft btn-success float-end"
                @click="$store.modalForm.openCreate({
                baseUrl: '{{ route('pets.store') }}',
                resourceName: 'Pet',
                defaults: {
                    type: '',
                    petpower: 0,
                    monsterflag: false,
                    temp: false,
                }
            })">
                <x-ui.icon name="add" /> New Pet
            </button>
        </x-top-links>

        <x-ui.table>
            <x-slot:head>
                <tr>
                    <th scope="col" class="w-[5%]">@sortablelink('level', 'Lvl')</th>
                    <th scope="col" class="w-[15%]">Spell</th>
                    <th scope="col">@sortablelink('type', 'Name')</th>
                    <th scope="col" class="w-[5%]">@sortablelink('petpower', 'Power')</th>
                    <th scope="col" class="w-[15%]">Race/Class</th>
                    <th scope="col" class="w-[10%] hidden md:table-cell">@sortablelink('hp', 'HP')</th>
                    <th scope="col" class="w-[10%] hidden lg:table-cell">@sortablelink('ac', 'AC')</th>
                    <th scope="col" class="w-[10%] hidden lg:table-cell">DMG</th>
                    <th scope="col" class="w-[10%] text-right">-</th>
                </tr>
            </x-slot:head>
            <x-slot:body>
                @foreach ($pets as $pet)
                    @php
                        $spell = $allSpells->firstWhere('teleport_zone', $pet->type);
                    @endphp
                    <tr x-data data-pet='@json($pet)'>
                        <td scope="row">{{ $pet->npc ? $pet->npc->level : 'N/A' }}</td>
                        <td>
                            @if ($spell?->id)
                                <x-spell-link
                                    :spell_id="$spell->id"
                                    :spell_name="$spell->name"
                                    :spell_icon="$spell->new_icon"
                                    spell_class="flex text-base"
                                />
                            @else
                                Unknown
                            @endif
                        </td>
                        <td>
                            <x-pet-link
                                :pet_id="$pet->id"
                                :pet_name="$pet->type"
                                pet_class="flex"
                            />
                        </td>
                        <td>{{ $pet->petpower }}</td>
                        <td class="hidden lg:table-cell">
                            {{ config('everquest.db_races.' . $pet->npc?->race) ?? 'Unknown' }} /
                            {{ config('everquest.classes.' . $pet->npc?->class) }}
                        </td>
                        <td class="hidden md:table-cell">{{ $pet->npc?->hp }}</td>
                        <td class="hidden lg:table-cell">{{ $pet->npc?->AC }}</td>
                        <td class="hidden lg:table-cell">
                            {{ $pet->npc?->mindmg }}-{{ $pet->npc?->maxdmg }}
                        </td>
                        <td class="text-right">
                            <div class="inline join">
                                <button type="button" class="join-item btn btn-sm btn-soft tooltip"
                                    data-tip="Edit"
                                    @click="$store.modalForm.openEdit(
                                        $el.closest('tr').dataset.pet,
                                        '{{ route('pets.update', $pet) }}',
                                        { resourceName: 'Edit Pet' }
                                    )">
                                    <x-ui.icon name="edit" />
                                </button>
                                <form action="{{ route('pets.destroy', $pet) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="join-item btn btn-sm btn-soft btn-error tooltip"
                                        data-tip="Delete"
                                        onclick="return confirm('Delete?')">
                                        <x-ui.icon name="delete" />
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-slot:body>
        </x-ui.table>

        <div class="mt-4 shrink-0">{{ $pets->links() }}</div>

        <x-modal-form>
            @include('pets.forms.form')
        </x-modal-form>
    </div>
@endsection
