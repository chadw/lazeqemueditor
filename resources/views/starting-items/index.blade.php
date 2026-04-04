@extends('layouts.app')
@section('title', 'Starting Items')
@section('page-title', 'Starting Items')

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">

        <x-top-links>
            <button type="button" class="btn btn-soft btn-success float-end"
                @click="$store.modalForm.openCreate({
                baseUrl: '{{ route('starting-items.store') }}',
                resourceName: 'Starting Item',
                defaults: {
                    item_id: 0,
                    item_charges: 1,
                    inventory_slot: -1,
                    status: 0,
                    class_list: 0,
                    race_list: 0,
                    deity_list: 0,
                    zone_id_list: 0,
                    augment_one: 0,
                    augment_two: 0,
                    augment_three: 0,
                    augment_four: 0,
                    augment_five: 0,
                    augment_six: 0,
                    min_expansion: -1,
                    max_expansion: -1,
                }
            })">
                <x-ui.icon name="add" /> New Starting Item
            </button>
        </x-top-links>

        <x-ui.table>
            <x-slot:head>
                <tr>
                    <th scope="col">Item</th>
                    <th scope="col" class="w-[5%]">Qty</th>
                    <th scope="col">Classes</th>
                    <th scope="col">Races</th>
                    <th scope="col">Deities</th>
                    <th scope="col">Zones</th>
                    <th scope="col" class="w-[10%] text-right">-</th>
                </tr>
            </x-slot:head>
            <x-slot:body>
                @forelse ($startingItems as $starting)
                    @php
                        $classList = collect(explode('|', $starting->class_list))
                            ->map(fn($id) => config('everquest.classes_abbr')[$id] ?? null)
                            ->filter()
                            ->implode(', ');

                        $raceList = collect(explode('|', $starting->race_list))
                            ->map(fn($id) => config('everquest.races')[$id] ?? null)
                            ->filter()
                            ->implode(', ');

                        $deityList = collect(explode('|', $starting->deity_list))
                            ->map(fn($id) => config('everquest.deity')[$id] ?? null)
                            ->filter()
                            ->implode(', ');

                        $zoneList = collect(explode('|', $starting->zone_id_list))
                            ->map(fn($id) => $zones[$id] ?? null)
                            ->filter()
                            ->implode(', ');

                        $starting->_classes = explode('|', $starting->class_list);
                        $starting->_races = explode('|', $starting->race_list);
                        $starting->_deities = explode('|', $starting->deity_list);
                        $starting->_zones = explode('|', $starting->zone_id_list);
                    @endphp

                    <tr x-data data-starting='@json($starting)'>
                        <td>
                            <x-item-link
                                :item_id="$starting->item->id"
                                :item_name="$starting->item->Name"
                                :item_icon="$starting->item->icon"
                                item_class="flex"
                            />
                        </td>
                        <td>{{ $starting->item_charges }}</td>
                        <td>{{ $classList ?: 'All' }}</td>
                        <td>{{ $raceList ?: 'All' }}</td>
                        <td>{{ $deityList ?: 'All' }}</td>
                        <td>{{ $zoneList ?: 'All' }}</td>
                        <td class="text-right">
                            <div class="inline join">
                                <button type="button" class="join-item btn btn-sm btn-soft tooltip"
                                    data-tip="Edit"
                                    @click="$store.modalForm.openEdit(
                                        $el.closest('tr').dataset.starting,
                                        '{{ route('starting-items.update', $starting) }}',
                                        { resourceName: 'Edit Starting Item' }
                                    )">
                                    <x-ui.icon name="edit" />
                                </button>
                                <form action="{{ route('starting-items.destroy', $starting) }}" method="POST"
                                    class="inline">
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
                        <td colspan="7" class="text-center py-6 text-base-content/50">
                            No starting items found.
                        </td>
                    </tr>
                @endforelse
            </x-slot:body>
        </x-ui.table>

        <div class="mt-4">{{ $startingItems->links() }}</div>

        <x-modal-form>
            @include('starting-items.forms.form')
        </x-modal-form>
    </div>
@endsection
