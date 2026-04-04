@extends('layouts.app')
@section('title', 'Alt Currency Characters')
@section('page-title', 'Alt Currency Characters')

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">

        <x-top-links>
            <x-slot name="left">
                @include('alt-currency.partials.filter-characters')
            </x-slot>

            <a href="{{ route('alt-currency.index') }}" class="btn btn-soft btn-accent">
                Items
            </a>
            <a href="{{ route('alt-currency.npcs.index') }}" class="btn btn-soft btn-accent">
                NPCs
            </a>
            <button type="button" class="btn btn-soft btn-success"
                @click="$store.modalForm.openCreate({
                    baseUrl: '{{ route('alt-currency.characters.store') }}',
                    resourceName: 'Alt Currency Character'
                })">
                <x-ui.icon name="add" /> New Alt Currency Character
            </button>
        </x-top-links>

        <x-search-results :items="$altChars" title="Alt Currency Characters">
            <x-ui.table>
                <x-slot:head>
                    <tr>
                        <th scope="col" class="w-[5%]">@sortablelink('char_id', 'Char ID')</th>
                        <th scope="col" class="w-[15%]">@sortablelink('char_name', 'Name')</th>
                        <th scope="col">@sortablelink('amount', 'Alt Currency')</th>
                        <th scope="col" class="w-[10%] text-right">-</th>
                    </tr>
                </x-slot:head>
                <x-slot:body>
                    @foreach ($altChars as $achar)
                        @php
                            $ac = $altCurrency->firstWhere('id', $achar->currency_id);
                        @endphp
                        <tr
                            x-data
                            data-altchar='@json($achar)'
                            class="{{ $achar->character ? '' : 'bg-error/20! text-error' }}"
                        >
                            <td>{{ $achar->char_id }}</td>
                            <td>
                                <a class="text-base link-accent link-hover"
                                    href="{{ route('characters.show', $achar->character?->id) }}">
                                    {{ $achar->character->name ?? 'Missing Character' }}
                                </a>
                            </td>
                            <td>
                                <div class="flex flex-inline">
                                    @if ($ac && $ac->item)
                                        <x-item-link
                                            :item_id="$ac->item->id"
                                            :item_name="'(' . $ac->item->id . ') ' . $ac->item->Name"
                                            :item_icon="$ac->item->icon"
                                            item_class="flex"
                                        />
                                    @else
                                        <span class="italic text-gray-500">Missing Alt Currency Item</span>
                                    @endif
                                    <span class="ml-2 badge badge-dash badge-success font-bold">
                                        {{ $achar->amount }}
                                    </span>
                                </div>
                            </td>
                            <td class="text-right items-end space-x-2">
                                <div class="inline join">
                                    <button type="button" class="join-item btn btn-sm btn-soft tooltip" data-tip="Edit"
                                        @click="$store.modalForm.openEdit(
                                            $el.closest('tr').dataset.altchar,
                                            '{{ route('alt-currency.characters.update', ['character' => $achar->char_id]) }}',
                                            { resourceName: 'Edit Alt Currency Character' }
                                        )">
                                        <x-ui.icon name="edit" />
                                    </button>
                                    <form
                                        action="{{ route('alt-currency.characters.destroy', [
                                            $achar->char_id,
                                            $achar->currency_id
                                        ]) }}"
                                        method="POST" class="inline">
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
        </x-search-results>

        <div class="mt-4">{{ $altChars->links() }}</div>

        <x-modal-form>
            @include('alt-currency.forms.character')
        </x-modal-form>
    </div>
@endsection
