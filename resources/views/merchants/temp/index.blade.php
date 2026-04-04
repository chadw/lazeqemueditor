@extends('layouts.app')

@php
    $merchantTitle = "Merchant Temp Items";
    if (request()->has('npc') && isset($selectedNpc)) {
        $merchantTitle = "Merchant Temp Items - Inventory for {$selectedNpc->clean_name} (Merchant ID: {$selectedNpc->id})";
    }
@endphp

@section('title', $merchantTitle)
@section('page-title', $merchantTitle)

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">

        <x-top-links>
            <x-slot name="left">
                @include('merchants.temp.partials.filters')
            </x-slot>
            @if (request()->has('npc'))
                <form action="{{ route('merchants.temp.clear-all', $selectedNpcId) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="zone" value="{{ request('zone') }}">
                    <input type="hidden" name="v" value="{{ request('v') }}">
                    <button type="submit" class="btn btn-soft btn-error"
                        onclick="return confirm('Are you sure you want to wipe ALL temporary items for this NPC? This cannot be undone.')">
                        <x-ui.icon name="delete" /> Clear All Temp Items
                    </button>
                </form>
            @endif
            {{--  adding/editing temp items not done --}}
        </x-top-links>

        <div class="mb-4">
            <x-ui.alert-info>
                <h3 class="font-bold">Temporary Item Notice</h3>
                Temporary items appear to be cached by the server. Deleting an item here removes it from the database,
                but it will remain visible on the merchant in game until the next <strong>server restart</strong>.
                In addition, setting the rule "World:ClearTempMerchantlist" to false/unchecked will still wipe this
                merchant's temp items on server restart.
            </x-ui.alert-info>
        </div>

        @if (request()->has('npc'))
            <x-search-results :items="$tempItems" title="Temp Merchant Items">
                <x-ui.table>
                    <x-slot:head>
                        <tr>
                            <th scope="col" class="w-[5%]">Slot</th>
                            <th scope="col">Item</th>
                            <th scope="col" class="w-[10%]">Buy Price</th>
                            <th scope="col" class="w-[10%]">Sell Price</th>
                            <th scope="col">Zone</th>
                            <th scope="col" class="w-[5%]">Instance ID</th>
                            <th scope="col" class="w-[10%]">Qty</th>
                            <th scope="col" class="w-[10%] text-right">-</th>
                        </tr>
                    </x-slot:head>
                    <x-slot:body>
                        @foreach ($tempItems as $item)
                            <tr x-data data-temp='@json($item)'>
                                <td scope="row">
                                    {{ $item->slot }}
                                </td>
                                <td>
                                    <x-item-link
                                        :item_id="$item->items->id"
                                        :item_name="$item->items->Name"
                                        :item_icon="$item->items->icon"
                                        item_class="flex"
                                    />
                                </td>
                                <td>
                                    <x-currency :value="$item->items->buy_cost" />
                                </td>
                                <td>
                                    <x-currency :value="$item->items->sell_price" />
                                </td>
                                <td>{{ $item->zone->short_name }}</td>
                                <td>{{ $item->instance_id }}</td>
                                <td>{{ $item->charges }}</td>
                                <td class="text-right">
                                    <div class="inline join">
                                        {{-- <button type="button" class="join-item btn btn-sm btn-soft tooltip"
                                            data-tip="Edit"
                                            @click="$store.modalForm.openEdit(
                                            $el.closest('tr').dataset.merchant,
                                            '{{ route('merchants.temp.update', $item->npcid) }}',
                                            { resourceName: 'Edit Temp Merchant Item' }
                                        )">
                                            <x-ui.icon name="edit" />
                                        </button> --}}
                                        <form action="{{ route('merchants.temp.destroy', [
                                                $item->npcid, $item->slot, $item->zone_id, $item->instance_id
                                            ]) }}"
                                            method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')

                                            <button class="join-item btn btn-sm btn-soft btn-error tooltip"
                                                data-tip="Delete"
                                                onclick="return confirm('Delete this temp item?')">
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
        @else
            <x-ui.alert-info>
                Please select a Zone and NPC to view/edit its merchants.
            </x-ui.alert-info>
        @endif

        <div class="mt-4">{{ $tempItems->links() }}</div>

        {{-- <x-modal-form>
            @include('merchants.partials.form')
        </x-modal-form> --}}
    </div>
@endsection
