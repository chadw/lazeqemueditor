@extends('layouts.app')
@section('title', 'Parcels')
@section('page-title', 'Parcels')

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">

        <x-top-links>
            <x-slot name="left">
                @include('parcels.partials.filters')
            </x-slot>
            <button type="button" class="btn btn-soft btn-success float-end"
                @click="$store.modalForm.openCreate({
                    baseUrl: '{{ route('parcels.store') }}',
                    resourceName: 'Parcel',
                    defaults: {
                        aug_slot_1: 0,
                        aug_slot_2: 0,
                        aug_slot_3: 0,
                        aug_slot_4: 0,
                        aug_slot_5: 0,
                        aug_slot_6: 0,
                        quantity: 1,
                        evolve_amount: 0,
                        from_name: 'System',
                    }
                })">
                <x-ui.icon name="add" /> New Parcel
            </button>
        </x-top-links>

        <x-search-results :items="$parcels" title="Parcels">
            <x-ui.table>
                <x-slot:head>
                    <tr>
                        <x-th-sort field="id" label="ID" class="w-[10%]" />
                        <x-th-sort field="character" label="To" class="w-[20%]" />
                        <x-th-sort field="item" label="Item" class="w-[30%]" />
                        <th scope="col" class="w-[10%] hidden md:table-cell">Qty</th>
                        <x-th-sort field="from_name" label="From" class="w-[20%] hidden md:table-cell" />
                        <x-th-sort field="sent_date" label="Sent" class="w-[10%] hidden md:table-cell" />
                        <th scope="col" class="w-[10%] text-right">-</th>
                    </tr>
                </x-slot:head>
                <x-slot:body>
                    @foreach ($parcels as $p)
                        <tr x-data data-parcel='@json($p)'>
                            <td>{{ $p->id }}</td>
                            <td>{{ $p->character->name ?? $p->char_id }}</td>
                            <td>
                                <x-item-link
                                    :item_id="$p->item->id"
                                    :item_name="$p->item->Name"
                                    :item_icon="$p->item->icon"
                                    item_class="flex"
                                />
                                @if ($p->container->isNotEmpty())
                                    <details class="dropdown dropdown-end mt-1" x-data="{ dropUp: false }"
                                        @click.outside="$el.open = false"
                                        @toggle="dropUp = $el.getBoundingClientRect().bottom > (window.innerHeight * 0.6)"
                                        :class="{ 'dropdown-top': dropUp }">
                                        <summary class="badge badge-soft badge-sm badge-info cursor-pointer gap-1">
                                            {{ $p->container->count() }} {{ Str::plural('item', $p->container->count()) }}
                                            inside
                                        </summary>
                                        <div
                                            class="dropdown-content z-50 mt-1 rounded-box bg-base-200 border border-base-content/10 shadow-lg p-3 w-64">
                                            <div class="grid grid-cols-4 gap-2">
                                                @foreach ($p->container as $ci)
                                                    @if ($ci->item)
                                                        <div x-data class="relative">
                                                            <a href="{{ route('items.edit', $ci->item->id) }}"
                                                                @mouseenter="$store.tooltip.loadTooltip('{{ route('items.popup', $ci->item->id) }}', $el, $event)"
                                                                @mouseleave="$store.tooltip.hideTooltip()"
                                                                class="flex items-center justify-center bg-base-100 rounded border border-base-100 p-1 h-12 w-full hover:bg-base-300 transition-colors"
                                                                title="{{ $ci->item->Name }}">
                                                                <span class="icon-wrap" aria-hidden="true">
                                                                    <span
                                                                        class="item-icon item-{{ $ci->item->icon }} item-icon-sm"></span>
                                                                </span>
                                                            </a>
                                                            @if (($ci->quantity ?? 1) > 1)
                                                                <span
                                                                    class="badge badge-xs badge-soft badge-accent absolute -bottom-1 -right-1">{{ $ci->quantity }}</span>
                                                            @endif
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </details>
                                @endif
                            </td>
                            <td>{{ $p->quantity ?? '-' }}</td>
                            <td>{{ $p->from_name }}</td>
                            <td>
                                {{ $p->sent_date ? \Carbon\Carbon::parse($p->sent_date)->format('Y-m-d H:i') : '' }}
                            </td>
                            <td class="text-right">
                                <div class="join">
                                    <button type="button" class="join-item btn btn-sm btn-soft tooltip" data-tip="Edit"
                                        @click="$store.modalForm.openEdit(
                                            $el.closest('tr').dataset.parcel,
                                            '{{ route('parcels.update', $p) }}',
                                            { resourceName: 'Edit Parcel' }
                                        )">
                                        <x-ui.icon name="edit" />
                                    </button>
                                    <form action="{{ route('parcels.destroy', $p) }}" method="POST" class="inline">
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

        <div class="mt-4">{{ $parcels->links() }}</div>

        <x-modal-form>
            @include('parcels.forms.form')
        </x-modal-form>
    </div>
@endsection
