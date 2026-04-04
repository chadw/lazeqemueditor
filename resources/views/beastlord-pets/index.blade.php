@extends('layouts.app')
@section('title', 'Beastlord Pets')
@section('page-title', 'Beastlord Pets')

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">

        <x-top-links>
            <button type="button" class="btn btn-soft btn-success float-end"
                @click="$store.modalForm.openCreate({
                baseUrl: '{{ route('beastlord-pets.store') }}',
                resourceName: 'Beastlord Pet',
                defaults: {
                    player_race: 0,
                    pet_race: 0,
                    texture: 0,
                    helm_texture: 0,
                    face: 0,
                    gender: 2,
                    size_modifier: 0,
                }
            })">
                <x-ui.icon name="add" /> New Beastlord Pet
            </button>
        </x-top-links>

        <x-ui.table>
            <x-slot:head>
                <tr>
                    <th scope="col">Race</th>
                    <th scope="col" class="w-[20%]">Pet Race</th>
                    <th scope="col" class="w-[10%]">Size Mod</th>
                    <th scope="col" class="w-[10%] text-right">-</th>
                </tr>
            </x-slot:head>
            <x-slot:body>
                @foreach ($beastlordPets as $blpet)
                    <tr x-data data-blpet='@json($blpet)'>
                        <td scope="row">{{ config('everquest.races.' . $blpet->player_race) }}</td>
                        <td>{{ config('everquest.db_races.' . $blpet->pet_race) }}</td>
                        <td>{{ $blpet->size_modifier }}</td>
                        <td class="text-right">
                            <div class="inline join">
                                <button type="button" class="join-item btn btn-sm btn-soft tooltip" data-tip="Edit"
                                    @click="$store.modalForm.openEdit(
                                        $el.closest('tr').dataset.blpet,
                                        '{{ route('beastlord-pets.update', $blpet) }}',
                                        { resourceName: 'Edit Beastlord Pet' }
                                    )">
                                    <x-ui.icon name="edit" />
                                </button>
                                <form action="{{ route('beastlord-pets.destroy', $blpet) }}" method="POST"
                                    class="inline">
                                    @csrf @method('DELETE')
                                    <button class="join-item btn btn-sm btn-soft btn-error tooltip" data-tip="Delete"
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

        <x-modal-form>
            @include('beastlord-pets.forms.form')
        </x-modal-form>
    </div>
@endsection
