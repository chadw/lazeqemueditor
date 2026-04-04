@php
    $nextDoorId = (($zone->doors()->max('doorid')) ?? 0) + 1;
    $zoneObjectDefaults = [
        'zone' => $zone->short_name,
        'version' => $zone->version,
        'xpos' => 0,
        'ypos' => 0,
        'zpos' => 0,
        'heading' => 0,
        'charges' => 0,
        'type' => 0,
        'icon' => 0,
        'size_percentage' => 0,
        'unknown24' => 0,
        'unknown60' => 0,
        'unknown64' => 0,
        'unknown68' => 0,
        'unknown72' => 0,
        'unknown76' => 0,
        'unknown84' => 0,
        'size' => 100,
        'solid_type' => 0,
        'incline' => 0,
        'tilt_x' => 0,
        'tilt_y' => 0,
    ];
@endphp
@push('scripts')
<script>
    window.zoneObjectDefaults = @json($zoneObjectDefaults);
</script>
@endpush

<x-ui.card>
    <x-slot:header>
        <h3 class="card-title">Objects</h3>
        <button type="button" class="btn btn-sm btn-soft btn-success"
            @click="$store.modalForm.openCreate({
                baseUrl: '{{ route('zones.objects.store', ['zone' => $zone->zoneidnumber]) }}',
                resourceName: 'Objects',
                modal: 'objects',
                defaults: zoneObjectDefaults
        })">
            <x-ui.icon name="add" /> Add
        </button>
    </x-slot:header>
    <x-ui.table>
        <x-slot:head>
            <tr>
                <th scope="col" class="w-[15%]">Type</th>
                <th scope="col" class="w-[10%]">Name</th>
                <th scope="col">Item</th>
                <th scope="col" class="w-[5%]">Charges</th>
                <th scope="col" class="w-[20%]">Coords</th>
                <th scope="col" class="w-[5%]">Version</th>
                <th scope="col" class="w-[10%] text-right">-</th>
            </tr>
        </x-slot:head>
        <x-slot:body>
            @forelse ($zone->objects as $obj)
                <tr x-data data-obj='@json($obj)'>
                    <td>{{ config('everquest.object_containers.' . $obj->type) ?? 'Unknown' }}</td>
                    <td>{{ $obj->objectname }}</td>
                    <td>
                        @if ($obj->item?->id)
                            <x-item-link
                                :item_id="$obj->item->id"
                                :item_name="$obj->item->Name"
                                :item_icon="$obj->item->icon"
                                item_class="flex"
                            />
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ $obj->charges }}</td>
                    <td>
                        x: {{ floor($obj->xpos) }},
                        y: {{ floor($obj->ypos) }},
                        z: {{ floor($obj->zpos) }},
                        heading: {{ $obj->heading }}
                    </td>
                    <td>{{ $obj->version }}</td>
                    <td class="text-right">
                        <div class="join">
                            <button type="button" class="join-item btn btn-sm btn-soft"
                                @click="$store.modalForm.openEdit(
                                    $el.closest('tr').dataset.obj,
                                    '{{ route('zones.objects.update', ['zone' => $zone->zoneidnumber, 'obj' => $obj->id]) }}',
                                    {
                                        modal: 'objects',
                                        resourceName: 'Edit Object',
                                    }
                                )">
                                <x-ui.icon name="edit" />
                            </button>
                            <form
                                action="{{ route('zones.objects.destroy', ['zone' => $zone->zoneidnumber, 'obj' => $obj->id]) }}"
                                method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button class="join-item btn btn-sm btn-soft btn-error"
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
                        No objects found.
                    </td>
                </tr>
            @endforelse
        </x-slot:body>
    </x-ui.table>
    <x-slot:footer>
        <div></div>
        <button type="button" class="btn btn-sm btn-soft btn-success"
            @click="$store.modalForm.openCreate({
                baseUrl: '{{ route('zones.objects.store', ['zone' => $zone->zoneidnumber]) }}',
                resourceName: 'Objects',
                modal: 'objects',
                defaults: zoneObjectDefaults
        })">
            <x-ui.icon name="add" /> Add
        </button>
    </x-slot:footer>
</x-ui.card>
