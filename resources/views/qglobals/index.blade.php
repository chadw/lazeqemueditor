@extends('layouts.app')
@section('page-title', 'Quest Globals')

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">

        <x-top-links>
            <x-slot name="left">
                @include('qglobals.partials.filters')
            </x-slot>
            <button type="button" class="btn btn-soft btn-success"
                @click="$store.modalForm.openCreate({
                    baseUrl: '{{ route('qglobals.store') }}',
                    resourceName: 'Quest Global'
                })">
                <x-ui.icon name="add" /> New Quest Global
            </button>
        </x-top-links>

        <x-search-results :items="$qglobals" title="Quest Globals">
            <x-ui.table>
                <x-slot:head>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col" class="w-[10%]">Value</th>
                        <th scope="col" class="w-[10%]">Character</th>
                        <th scope="col" class="w-[10%]">NPC</th>
                        <th scope="col" class="w-[10%]">Zone</th>
                        <th scope="col" class="w-[10%]">Expires</th>
                        <th scope="col" class="w-[10%]">-</th>
                    </tr>
                </x-slot:head>
                <x-slot:body>
                    @foreach ($qglobals as $qglobal)
                        <tr x-data data-qglobal='@json($qglobal)'>
                            <td>{{ $qglobal->name }}</td>
                            <td>{{ $qglobal->value ?? '-' }}</td>
                            <td>
                                <x-status-indicator :ok="$qglobal->character">
                                    {{ $qglobal->character->name ?? '' }}
                                </x-status-indicator>
                            </td>
                            <td>
                                <x-status-indicator :ok="$qglobal->npc">
                                    {{ $qglobal->npc->name ?? '' }}
                                </x-status-indicator>
                            </td>
                            <td>
                                <x-status-indicator :ok="$qglobal->zone">
                                    {{ $qglobal->zone->short_name ?? '' }}
                                </x-status-indicator>
                            </td>
                            <td>
                                {{ $qglobal->expdate != ''
                                    ? \Carbon\Carbon::parse($qglobal->expdate)->format('Y-m-d H:i')
                                    : 'Never' }}
                            </td>
                            <td class="text-right">
                                <div class="inline join">
                                    <button type="button" class="join-item btn btn-sm btn-soft tooltip"
                                        data-tip="Edit"
                                        @click="$store.modalForm.openEdit(
                                            $el.closest('tr').dataset.qglobal,
                                            '{{ route('qglobals.update', $qglobal) }}',
                                            { resourceName: 'Edit Quest Global' }
                                        )">
                                        <x-ui.icon name="edit" />
                                    </button>
                                    <form action="{{ route('qglobals.destroy') }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <input type="hidden" name="name" value="{{ $qglobal->name }}">
                                        <input type="hidden" name="charid" value="{{ $qglobal->charid }}">
                                        <input type="hidden" name="npcid" value="{{ $qglobal->npcid }}">
                                        <input type="hidden" name="zoneid" value="{{ $qglobal->zoneid }}">
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
        </x-search-results>

        <div class="mt-4 shrink-0">{{ $qglobals->links() }}</div>

        <x-modal-form>
            @include('qglobals.forms.form')
        </x-modal-form>
    </div>
@endsection
