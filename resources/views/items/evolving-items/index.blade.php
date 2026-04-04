@extends('layouts.app')
@section('title', 'Evolving Item Details')
@section('page-title', 'Evolving Item Details')

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">

        <x-top-links>
            <a href="{{ route('items.index') }}" class="btn btn-soft btn-accent">
                Items
            </a>
            <button type="button" class="btn btn-soft btn-success"
                @click="$store.modalForm.openCreate({
                baseUrl: '{{ route('items.evolving-items.store') }}',
                resourceName: 'Evolving Item Details',
                defaults: {
                    type: 1,
                    item_evolve_level: 1,
                    required_amount: 0,
                }
            })">
                <x-ui.icon name="add" /> New Evolving Item Details
            </button>
        </x-top-links>

        <div class="space-y-6">
        @foreach ($groups as $group)
            <div class="card bg-base-100 shadow">
                <h2 class="card-title p-4 flex items-center justify-between">
                    <span>Evolvution ID: {{ $group->item_evo_id }}</span>
                    @php
                    $groupId = $group->item_evo_id;
                    $nextLevel = ($items->get($groupId)->max('item_evolve_level') ?? 0) + 1;
                    @endphp

                    <button type="button" class="btn btn-sm btn-soft btn-success"
                    @click="$store.modalForm.openCreate({
                        baseUrl: '{{ route('items.evolving-items.store') }}',
                        resourceName: 'Evolving Item Details',
                        defaults: {
                            type: 1,
                            item_evo_id: {{ $groupId }},
                            item_evolve_level: {{ $nextLevel }},
                            required_amount: 0,
                        }
                    })">
                    <x-ui.icon name="add" /> Add new for {{ $groupId }}
                    </button>
                </h2>
                <div class="border border-base-content/5 overflow-x-auto">
                    <table class="table table-auto table-zebra md:table-fixed w-full">
                        <thead class="text-xs uppercase bg-neutral">
                            <tr>
                                <th scope="col" class="w-[5%]">Level</th>
                                <th scope="col">Item</th>
                                <th scope="col" class="w-[10%]">Type</th>
                                <th scope="col">SubType</th>
                                <th scope="col" class="w-[10%]">Req Amount</th>
                                <th scope="col" class="w-[10%] text-right">-</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items->get($group->item_evo_id, collect()) as $detail)
                                @php
                                    // 1. Split the subtype by the dot delimiter
                                    $subTypes = explode('.', $detail->sub_type);

                                    // 2. Identify which source array to use based on type
                                    $sourceArray = match((int)$detail->type) {
                                        1 => config('everquest.evolving_item_subtypes'),
                                        3 => config('everquest.db_races'), // Your Race Array
                                        4 => $zones, // Your Zones collection/array
                                        default => []
                                    };
                                @endphp
                                @php
                                    $evolvingPayload = $detail->toArray();
                                    $stArr = array_filter(explode('.', $detail->sub_type), fn($s) => $s !== '');
                                    $evolvingPayload['sub_type'] = array_map(function ($s) {
                                        return is_numeric($s) ? (int) $s : $s;
                                    }, $stArr);
                                @endphp
                                <tr x-data data-evolving='@json($evolvingPayload)'>
                                    <td scope="row">
                                        {{ $detail->item_evolve_level }}
                                    </td>
                                    <td>
                                        <x-item-link
                                            :item_id="$detail->item->id"
                                            :item_name="$detail->item->Name"
                                            :item_icon="$detail->item->icon"
                                            item_class="flex"
                                        />
                                    </td>
                                    <td>
                                        {{ config('everquest.evolving_item_types')[$detail->type] ?? 'Unknown' }}
                                    </td>
                                    <td>
                                        @if ($sourceArray)
                                            @foreach($subTypes as $subId)
                                                @php
                                                    // Handle Zones (Collection vs Array)
                                                    if ($detail->type == 4 && is_object($sourceArray)) {
                                                        $label = $sourceArray->firstWhere('zoneidnumber', $subId)->short_name ?? $subId;
                                                    } else {
                                                        $label = $sourceArray[$subId] ?? $subId;
                                                    }
                                                @endphp
                                                <div class="inline-flex items-center rounded-md bg-base-300 overflow-hidden border border-base-content/10 mr-1 mb-1">
                                                    <span class="px-2 py-1 bg-neutral text-neutral-content/50 text-xs font-mono font-bold">
                                                        {{ $subId }}
                                                    </span>
                                                    <span class="px-2 py-1 text-xs whitespace-nowrap">
                                                        {{ $label }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        @else
                                            {{ config('everquest.evolving_item_subtypes.' . $detail->sub_type) }}
                                        @endif
                                    </td>
                                    <td>{{ $detail->required_amount }}</td>
                                    <td class="text-right">
                                        <div class="inline join">
                                            <button type="button" class="join-item btn btn-sm btn-soft tooltip"
                                                data-tip="Edit"
                                                @click="$store.modalForm.openEdit(
                                                    $el.closest('tr').dataset.evolving,
                                                    '{{ route('items.evolving-items.update', $detail) }}',
                                                    { resourceName: 'Edit Evolving Item Details' }
                                                )">
                                                <x-ui.icon name="edit" />
                                            </button>
                                            <form action="{{ route('items.evolving-items.destroy', $detail) }}"
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
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
        </div>

        <div class="mt-4">{{ $groups->links() }}</div>

        <x-modal-form height="min-h-[50vh]">
            @include('items.evolving-items.forms.form')
        </x-modal-form>
    </div>
@endsection
