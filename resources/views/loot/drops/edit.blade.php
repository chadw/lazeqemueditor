@extends('layouts.app')
@section('title', 'Edit Loot Drop')
@section('page-title', 'Edit Loot Drop')

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">

        <x-top-links>
            <x-slot name="left">
                <a href="{{ route('loot.drops.index') }}" class="btn btn-sm btn-soft">Back to Drops</a>
            </x-slot>
        </x-top-links>

        <div class="card bg-base-200 mb-6">
            <div class="card-body">
                <form method="POST" action="{{ route('loot.drops.update', $drop) }}">
                    @csrf
                    @method('PUT')
                    @include('loot.drops.forms.form')
                </form>
            </div>
        </div>

        @if(isset($loottable) && $loottable)
            @include('loot.partials.loottable-npcs', ['table' => $loottable])
        @endif

        <div class="card bg-base-100 shadow-sm border border-base-300">
            <div class="bg-neutral text-neutral-content px-4 py-3 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <h3 class="font-bold">
                        <a href="#" class="text-base link-accent link-hover" @click.prevent="$store.modalForm.openCreate({
                            modal: 'lootdrop-tables',
                            resourceName: 'Loot Tables using {{ addslashes($drop->name) }}',
                            defaults: { drop: { id: {{ $drop->id }}, name: '{{ addslashes($drop->name) }}' } },
                            meta: { url: '{{ route('loot.drops.tables', $drop) }}' }
                        })">
                            <span>{{ $drop->name }} (ID: {{ $drop->id }})</span>
                        </a>
                    </h3>
                </div>
                <div>
                    <button type="button" class="btn btn-sm btn-soft btn-success"
                        @click="$store.modalForm.openCreate({
                            baseUrl: '{{ route('loot.drops.entries.store', $drop->id) }}',
                            resourceName: 'Loot Item',
                            modal: 'lootdrop-items',
                            defaults: {
                                chance: 1,
                                disabled_chance: 0,
                                item_charges: 1,
                                equip_item: false,
                                multiplier: 1,
                            }
                    })">
                        <x-ui.icon name="add" /> Add Item
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="table table-zebra table-sm w-full">
                    <thead class="bg-base-300/50">
                        <tr>
                            <th class="w-[5%]">Item ID</th>
                            <th>Item Name</th>
                            <th class="w-[10%]">Chance</th>
                            <th class="w-[10%]">Charges</th>
                            <th class="w-[10%]">Equip</th>
                            <th class="w-[15%] text-right">-</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($drop->entries as $entry)
                            <tr x-data data-dropentry='@json($entry)'>
                                <td>{{ $entry->item->id }}</td>
                                <td>
                                    @if($entry->item)
                                        <x-item-link
                                            :item_id="$entry->item->id"
                                            :item_name="$entry->item->Name"
                                            :item_icon="$entry->item->icon"
                                            item_class="flex"
                                        />
                                    @else
                                        <span class="italic opacity-60">Item #{{ $entry->item_id }} (not found)</span>
                                    @endif
                                </td>
                                <td>{{ $entry->chance }}%</td>
                                <td>{{ $entry->item_charges ?? '0' }}</td>
                                <td>
                                    @if($entry->equip_item)
                                        <span class="badge badge-sm badge-soft badge-success">Yes</span>
                                    @else
                                        <span class="badge badge-sm badge-soft badge-error">No</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <div class="join">
                                        <button type="button" class="join-item btn btn-sm btn-soft"
                                            @click="$store.modalForm.openEdit(
                                                $el.closest('tr').dataset.dropentry,
                                                '{{ route('loot.drops.entries.update', ['drop' => $drop->id, 'item' => $entry->item_id]) }}',
                                                {
                                                    modal: 'lootdrop-items',
                                                    resourceName: 'Edit Loot Drop Item'
                                                }
                                            )"
                                        >
                                            <x-ui.icon name="edit" />
                                        </button>
                                        <form method="POST" action="{{ route('loot.drops.entries.destroy', ['drop' => $drop->id, 'item' => $entry->item_id]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="join-item btn btn-sm btn-soft btn-error" onclick="return confirm('Remove item from loot drop?')">
                                                <x-ui.icon name="delete" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center opacity-60">No items in this loot drop</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @php
                $totalChance = $drop->entries->sum('chance');
                $percent = min($totalChance, 100);
            @endphp
            <div class="card-actions justify-between p-2 bg-base-300/20 border-t border-base-300">
                <div class="flex items-center gap-3 text-xs font-semibold">
                    <div class="flex items-center gap-2">
                        <span class="opacity-70 uppercase tracking-wider">Total Chance</span>
                        <div class="w-32 bg-base-200 rounded-full h-2 overflow-hidden">
                            <div class="h-full transition-all duration-300 {{ $totalChance == 100 ? 'bg-success' : ($totalChance > 100 ? 'bg-error' : 'bg-warning') }}" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                    <div class="badge badge-sm badge-soft {{ $totalChance == 100 ? 'badge-success' : ($totalChance > 100 ? 'badge-error' : 'badge-warning') }}">{{ $totalChance }} / 100%</div>
                </div>
                <div>
                    <button type="button" class="btn btn-xs btn-soft btn-success"
                        @click="$store.modalForm.openCreate({
                            baseUrl: '{{ route('loot.drops.entries.store', $drop->id) }}',
                            resourceName: 'Loot Item',
                            modal: 'lootdrop-items',
                            defaults: {
                                chance: 1,
                                disabled_chance: 0,
                                item_charges: 1,
                                equip_item: false,
                                multiplier: 1,
                            }
                    })">
                        <x-ui.icon name="add" /> Add Item
                    </button>
                </div>
            </div>
        </div>

        <x-modal-form x-show="$store.modalForm.isOpen">
            <template x-if="$store.modalForm.activeModal === 'lootdrop'">
                @include('loot.forms.lootdrop-form')
            </template>
            <template x-if="$store.modalForm.activeModal === 'lootdrop-items'">
                @include('loot.forms.lootdrop-item-form')
            </template>
            <template x-if="$store.modalForm.activeModal === 'lootdrop-tables'">
                @include('loot.drops.partials.modal-loottables')
            </template>
        </x-modal-form>
    </div>
@endsection
