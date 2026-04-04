@extends('layouts.app')
@section('title', 'Auras')
@section('page-title', 'Auras')

@section('content')
    <div x-data="" @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">

        <x-top-links>
            <button type="button" class="btn btn-soft btn-success"
                @click="$store.modalForm.openCreate({
                    baseUrl: '{{ route('auras.store') }}',
                    resourceName: 'Aura',
                    defaults: {
                        npc_type: '',
                        spell_id: '',
                        distance: 60,
                        aura_type: 1,
                        spawn_type: 0,
                        movement: 0,
                        duration: 5400,
                        icon: -1,
                        cast_time: -1,
                    }
                })">
                <x-ui.icon name="add" /> New Aura
            </button>
        </x-top-links>

        <x-ui.table>
            <x-slot:head>
                <tr>
                    <th scope="col" class="w-[5%]">NPC ID</th>
                    <th scope="col">Aura</th>
                    <th scope="col">Spell</th>
                    <th scope="col" class="w-[20%]">Type</th>
                    <th scope="col" class="w-[10%] text-right">-</th>
                </tr>
            </x-slot:head>
            <x-slot:body>
                @foreach ($auras as $aura)
                    <tr x-data data-aura='@json($aura)'>
                        <td>{{ $aura->npc_type }}</td>
                        <td>{{ $aura->name }}</td>
                        <td>
                            <x-spell-link
                                :spell_id="$aura->spell->id"
                                :spell_name="$aura->spell->name"
                                :spell_icon="$aura->spell->new_icon"
                                :spell_target_type="$aura->spell->targettype"
                                spell_class="inline-flex"
                                :effects_only="1"
                            />
                        </td>
                        <td>{{ config('everquest.aura_type')[$aura->aura_type] }}</td>
                        <td class="text-right">
                            <div class="join">
                                <button type="button" class="join-item btn btn-sm btn-soft tooltip" data-tip="Edit"
                                    @click="$store.modalForm.openEdit(
                                        $el.closest('tr').dataset.aura,
                                        '{{ route('auras.update', ['aura' => $aura->type]) }}',
                                        { resourceName: 'Edit Aura' }
                                    )">
                                    <x-ui.icon name="edit" />
                                </button>
                                <form action="{{ route('auras.destroy', $aura) }}" method="POST" class="inline">
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
            @include('auras.forms.form')
        </x-modal-form>
    </div>
@endsection
