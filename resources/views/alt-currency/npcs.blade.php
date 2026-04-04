@extends('layouts.app')
@section('title', 'Alt Currency Npcs')
@section('page-title', 'Alt Currency Npcs')

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">

        <x-top-links>
            <a href="{{ route('alt-currency.characters.index') }}" class="btn btn-soft btn-accent">
                Characters
            </a>
            <a href="{{ route('alt-currency.index') }}" class="btn btn-soft btn-accent">
                Items
            </a>
            <button type="button" class="btn btn-soft btn-success"
                @click="$store.modalForm.openCreate({
                    baseUrl: '{{ route('alt-currency.npcs.store') }}',
                    resourceName: 'Alt Currency Npc'
                })">
                <x-ui.icon name="add" /> New Alt Currency Npc
            </button>
        </x-top-links>

        <x-ui.table>
            <x-slot:head>
                <tr>
                    <th scope="col" class="w-[10%]">@sortablelink('id', 'Npc Id')</th>
                    <th scope="col" class="w-[10%]">@sortablelink('name', 'Name')</th>
                    <th scope="col">Spawn</th>
                    <th scope="col">@sortablelink('alt_currency_id', 'Alt Currency')</th>
                    <th scope="col" class="w-[10%] md:table-cell text-right">-</th>
                </tr>
            </x-slot:head>
            <x-slot:body>
                @foreach ($altNpcs as $anpc)
                    @php
                        $ac = $altCurrency->firstWhere('id', $anpc->alt_currency_id);
                        $loc = $anpc->firstSpawnEntries?->spawn2;
                        // for select prefill
                        $anpc['npc'] = ['id' => $anpc->id, 'name' => $anpc->clean_name];
                    @endphp
                    <tr x-data data-altnpc='@json($anpc)'>
                        <td>{{ $anpc->id }}</td>
                        <td>
                            <a class="text-base link-accent link-hover"
                                href="{{ route('merchants.index', [
                                    'zone' => $loc?->zoneData?->zoneidnumber ?? $loc?->zone,
                                    'v' => $loc?->version ?? 0,
                                    'npc' => $anpc->id,
                                ]) }}">
                                {{ $anpc->clean_name }}
                            </a>
                        </td>
                        <td>
                            {{ $loc?->zone ?? 'Unknown' }}
                            (<code>X: {{ $loc?->x ?? 'N/A' }}, Y: {{ $loc?->y ?? 'N/A' }}, Z: {{ $loc?->z ?? 'N/A' }}</code>)
                        </td>
                        <td>
                            <x-item-link
                                :item_id="$ac->item->id"
                                :item_name="'(' . $ac->item->id . ') ' . $ac->item->Name"
                                :item_icon="$ac->item->icon"
                                item_class="flex"
                            />
                        </td>
                        <td class="text-right items-end space-x-2">
                            <div class="inline join">
                                <button type="button" class="join-item btn btn-sm btn-soft tooltip" data-tip="Edit"
                                    @click="$store.modalForm.openEdit(
                                        $el.closest('tr').dataset.altnpc,
                                        '{{ route('alt-currency.npcs.update', $anpc) }}',
                                        { resourceName: 'Edit Alt Currency Npc' }
                                    )">
                                    <x-ui.icon name="edit" />
                                </button>
                                <form action="{{ route('alt-currency.npcs.destroy', $anpc) }}" method="POST"
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

        <div class="mt-4">{{ $altNpcs->links() }}</div>

        <x-modal-form>
            @include('alt-currency.forms.npc')
        </x-modal-form>
    </div>
@endsection
