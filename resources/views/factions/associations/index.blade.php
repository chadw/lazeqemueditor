@extends('layouts.app')
@section('title', 'Faction Associations')
@section('page-title', 'Faction Associations')

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">

        <x-top-links>
            <x-slot name="left">
                @include('factions.associations.partials.filters')
            </x-slot>
            <button type="button" class="btn btn-soft btn-success float-end"
                @click="$store.modalForm.openCreate({
                    baseUrl: '{{ route('factions.associations.store') }}',
                    resourceName: 'Faction Association'
                })">
                <x-ui.icon name="add" /> New Faction Association
            </button>
        </x-top-links>

        <x-search-results :items="$associations" title="Faction Associations">
            <x-ui.table>
                <x-slot:head>
                    <tr>
                        <th scope="col" class="w-[5%]">ID</th>
                        <th scope="col">Faction</th>
                        <th scope="col" class="w-[10%]">Factions Count</th>
                        <th scope="col" class="w-[10%] text-right">-</th>
                    </tr>
                </x-slot:head>
                <x-slot:body>
                    @forelse ($associations as $association)
                        <tr x-data data-association='@json($association)'>
                            <td>{{ $association->id }}</td>
                            <td>{{ $association->factionList->name }}</td>
                            <td>{{ $association->factions_count }}</td>
                            <td class="text-right">
                                <div class="join">
                                    <button type="button" class="join-item btn btn-sm btn-soft tooltip"
                                        data-tip="Edit"
                                        @click="$store.modalForm.openEdit(
                                            $el.closest('tr').dataset.association,
                                            '{{ route('factions.associations.update', $association) }}',
                                            { resourceName: 'Edit Faction Association' }
                                        )">
                                        <x-ui.icon name="edit" />
                                    </button>
                                    <form action="{{ route('factions.associations.destroy', $association) }}"
                                        method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button class="join-item btn btn-sm btn-soft btn-error"
                                            data-tip="Delete"
                                            onclick="return confirm('Delete?')">
                                            <x-ui.icon name="delete" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-6 text-base-content/50">
                                No Faction Associations found.
                            </td>
                        </tr>
                    @endforelse
                </x-slot:body>
            </x-ui.table>
        </x-search-results>

        <div class="mt-4">{{ $associations->links() }}</div>

        <x-modal-form width="max-w-3xl">
            @include('factions.associations.forms.form')
        </x-modal-form>
    </div>
@endsection
