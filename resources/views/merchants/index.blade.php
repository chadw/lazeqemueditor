@extends('layouts.app')

@php
    $merchantTitle = "Edit Merchants";
    if (request()->has('npc') && isset($selectedNpc)) {
        $merchantTitle = "Inventory for {$selectedNpc->clean_name} (Merchant ID: {$selectedNpc->merchant_id})";
    }
@endphp

@section('title', $merchantTitle)
@section('page-title', $merchantTitle)

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">

        <x-top-links>
            <x-slot name="left">
                @include('merchants.partials.filters')
            </x-slot>
            @if (request()->has('npc'))
                <a class="btn btn-soft btn-accent"
                    href="{{ route('merchants.temp.index', [
                        'merchant' => $selectedNpc->id,
                        ...request()->all()
                    ]) }}">
                    Temp Items
                </a>
            @endif
            @if(isset($selectedNpc) && $selectedNpc)
                <button type="button" class="btn btn-soft btn-success float-end"
                    @click="$store.modalForm.openCreate({
                    baseUrl: '{{ route('merchants.store', ['npc' => $selectedNpc->id]) }}',
                    resourceName: 'Merchant',
                    defaults: {
                        classes_required: 65535,
                        faction_required: -100,
                        level_required: 0,
                        probability: 100,
                        min_status: 0,
                        max_status: 255,
                        alt_currency_cost: 0,
                        bucket_name: '',
                        bucket_value: '',
                    }
                })">
                    <x-ui.icon name="add" /> New Merchant Item
                </button>
            @else
                <button type="button" class="btn btn-soft btn-success float-end" disabled title="Select an NPC to add items">
                    <x-ui.icon name="add" /> New Merchant Item
                </button>
            @endif
        </x-top-links>

        @if($npcs->isNotEmpty() && !request()->has('npc'))
            <h2 class="card-title mb-4">Found {{ number_format($npcs->count()) }} Merchants</h2>
            <div class="mb-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach($npcs as $npc)
                        <div class="p-3 border border-base-content/10 rounded">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-medium">
                                        {{ $npc->clean_name }}
                                        <span class="badge badge-sm badge-soft badge-accent ml-2">
                                            {{ $npc->merchant_item_count ?? 0 }}
                                            {{ Str::plural('Item', $npc->merchant_item_count ?? 0) }}
                                        </span>
                                    </div>
                                    <div class="text-sm text-muted">ID: {{ $npc->id }} @if($npc->merchant_id) · Merchant ID: {{ $npc->merchant_id }}@endif</div>
                                </div>
                                <div class="mt-1">
                                    <a href="{{ route('merchants.index', array_merge(request()->all(), ['npc' => $npc->id])) }}"
                                        class="btn btn-sm btn-soft btn-accent">
                                        <x-ui.icon name="show" />
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            @if (request()->query->count() > 0 && !request()->has('npc'))
                <div class="bg-base-100 border border-base-content/10 text-center py-6 text-base-content/50 mb-4">
                    No Merchants found using the search filters provided.
                </div>
            @endif
        @endif

        @if (request()->has('npc'))
            @if (!empty($merchantAltCurrency) && $merchantAltCurrency->item)
                <div class="mb-4 text-sm flex items-center gap-3">
                    <span class="font-semibold">Alt currency:</span>
                    <x-item-link
                        :item_id="$merchantAltCurrency->item->id"
                        :item_name="'(' . $merchantAltCurrency->id . ') ' . $merchantAltCurrency->item->Name"
                        :item_icon="$merchantAltCurrency->item->icon"
                        item_class="inline-flex items-center"
                    />
                </div>
            @endif
            <x-search-results :items="$merchantItems" title="Merchant Items">
                <x-ui.table>
                    <x-slot:head>
                        <tr>
                            <th scope="col" class="w-[5%]">@sortablelink('slot', 'Slot')</th>
                            <th scope="col">@sortablelink('item', 'Item')</th>
                            <th scope="col" class="w-[10%]">@sortablelink('buy', 'Buy Price')</th>
                            <th scope="col" class="w-[10%]">@sortablelink('sell', 'Sell Price')</th>
                            <th scope="col" class="w-[10%]">@sortablelink('alt_currency_cost', 'Alt Currency Cost')</th>
                            <th scope="col" class="w-[10%]">Faction Req</th>
                            <th scope="col" class="w-[10%]">Level Req</th>
                            <th scope="col" class="w-[10%]">Probability</th>
                            <th scope="col" class="w-[10%] text-right">-</th>
                        </tr>
                    </x-slot:head>
                    <x-slot:body>
                        @foreach ($merchantItems as $item)
                            <tr x-data data-merchant='@json($item)'>
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
                                <td>{{ $item->alt_currency_cost }}</td>
                                <td>{{ $item->faction_required }}</td>
                                <td>{{ $item->level_required }}</td>
                                <td>{{ $item->probability }}%</td>
                                <td class="text-right">
                                    <div class="inline join">
                                        <button type="button" class="join-item btn btn-sm btn-soft tooltip"
                                            data-tip="Edit"
                                            @click="$store.modalForm.openEdit(
                                                $el.closest('tr').dataset.merchant,
                                                '{{ route('merchants.update', $item->merchantid) }}',
                                                { resourceName: 'Edit Merchant Item' }
                                            )">
                                            <x-ui.icon name="edit" />
                                        </button>
                                        <form action="{{ route('merchants.destroy', ['merchant' => $item->merchantid, 'slot' => $item->slot]) }}"
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
                        @endforeach
                    </x-slot:body>
                </x-ui.table>
            </x-search-results>
        @else
            <x-ui.alert-info>
                Please select a Zone and NPC to view/edit its merchants.
            </x-ui.alert-info>
        @endif

        <x-modal-form>
            @include('merchants.forms.form')
        </x-modal-form>
    </div>
@endsection
