@extends('layouts.app')
@section('title', 'Character Factions')
@section('page-title', 'Character Factions')

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">

        <x-top-links>
            <x-slot name="left">
                @include('factions.characters.partials.filters')
            </x-slot>
            <button type="button" class="btn btn-soft btn-success float-end"
                @click="$store.modalForm.openCreate({
                    baseUrl: '{{ route('factions.characters.store') }}',
                    resourceName: 'Character Faction',
                    defaults: {
                        current_value: 0,
                    }
                })">
                <x-ui.icon name="add" /> New Character Faction
            </button>
        </x-top-links>

        <x-search-results :items="$factions" title="Character Factions">
            <x-ui.table>
                <x-slot:head>
                    <tr>
                        <th scope="col" class="w-[30%]">Character</th>
                        <th scope="col">Faction</th>
                        <th scope="col" class="w-[20%]">Value</th>
                        <th scope="col" class="w-[10%] text-right">-</th>
                    </tr>
                </x-slot:head>
                <x-slot:body>
                    @forelse ($factions as $faction)
                        <tr x-data data-fvalue='@json($faction)'>
                            <td>
                                <a href="{{ route('characters.show', $faction->char_id) }}"
                                    class="text-base link-info link-hover">
                                    {{ $faction->character?->name ?? 'Unknown' }}</a>
                                <span class="badge badge-sm badge-soft ml-1">
                                    {{ $faction->char_id }}
                                </span>
                            </td>
                            <td>
                                {{ $faction->faction?->name ?? 'Unknown' }}
                                <span class="badge badge-sm badge-soft ml-1">
                                    {{ $faction->faction_id }}
                                </span>
                            </td>
                            <td>
                                {!! $faction->standing !!}
                                <span class="badge badge-sm badge-soft ml-1">
                                    {{ $faction->current_value }}
                                </span>
                            </td>
                            <td class="text-right">
                                <div class="join">
                                    <button type="button" class="join-item btn btn-sm btn-soft tooltip"
                                        data-tip="Edit"
                                        @click="$store.modalForm.openEdit(
                                            $el.closest('tr').dataset.fvalue,
                                            '{{ route('factions.characters.update', [$faction->char_id, $faction->faction_id]) }}',
                                            { resourceName: 'Edit Character Faction' }
                                        )">
                                        <x-ui.icon name="edit" />
                                    </button>
                                    <form action="{{ route('factions.characters.destroy', [$faction->char_id, $faction->faction_id]) }}"
                                        method="POST" class="inline">
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
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-6 text-base-content/50">
                                No Factions for character found.
                            </td>
                        </tr>
                    @endforelse
                </x-slot:body>
            </x-ui.table>
        </x-search-results>

        <div class="mt-4">{{ $factions->links() }}</div>

        <x-modal-form>
            @include('factions.characters.forms.form')
        </x-modal-form>
    </div>
@endsection
