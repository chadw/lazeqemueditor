@php
    $nextNumberId = (($zone->zonepoints()->max('number')) ?? 0) + 1;
    $zonePointDefaults = [
        'zone' => $zone->short_name,
        'version' => $zone->version,
        'number' => $nextNumberId,
        'y' => 0,
        'x' => 0,
        'z' => 0,
        'heading' => 0,
        'target_y' => 0,
        'target_x' => 0,
        'target_z' => 0,
        'target_heading' => 0,
        'zoneinst' => 0,
        'target_zone_id' => 0,
        'target_instance' => 0,
        'buffer' => 0,
        'client_version_mask' => 4294967295,
        'height' => 0,
        'width' => 0,
    ];
@endphp
@push('scripts')
<script>
    window.zonePointDefaults = @json($zonePointDefaults);
</script>
@endpush

<x-ui.card>
    <x-slot:header>
        <h3 class="card-title">Zone Points</h3>
        <button type="button" class="btn btn-sm btn-soft btn-success"
            @click="$store.modalForm.openCreate({
                baseUrl: '{{ route('zones.zone-points.store', ['zone' => $zone->short_name]) }}',
                resourceName: 'Zone Point',
                modal: 'zone-point',
                defaults: window.zonePointDefaults
        })">
            <x-ui.icon name="add" /> Add
        </button>
    </x-slot:header>
    <x-ui.table>
        <x-slot:head>
            <tr>
                <th scope="col">Zone</th>
                <th scope="col" class="w-[20%]">From Coords</th>
                <th scope="col" class="w-[30%]">To Coords</th>
                <th scope="col" class="w-[5%]">Target Instance</th>
                <th scope="col">Target Zone</th>
                <th scope="col" class="w-[10%] text-right">-</th>
            </tr>
        </x-slot:head>
        <x-slot:body>
            @forelse ($zone->zonepoints as $zp)
                <tr x-data data-zonepoint='@json($zp)'>
                    <td>{{ $zp->zone }}</td>
                    <td>
                        x: {{ floor($zp->x) }},
                        y: {{ floor($zp->y) }},
                        z: {{ floor($zp->z) }},
                        heading: {{ floor($zp->heading) }}
                    </td>
                    <td>
                        x: {{ floor($zp->target_x) }},
                        y: {{ floor($zp->target_y) }},
                        z: {{ floor($zp->target_z) }},
                        heading: {{ floor($zp->target_heading) }}
                    </td>
                    <td>{{ $zp->target_instance ?? 0 }}</td>
                    <td>
                        @if ($zp->targetZones)
                            {{ $zp->targetZones->short_name }}
                        @else
                            {{ $zp->target_zone }}
                        @endif
                    </td>
                    <td class="text-right">
                        <div class="join">
                            <button type="button" class="join-item btn btn-sm btn-soft"
                                @click="$store.modalForm.openEdit(
                                    $el.closest('tr').dataset.zonepoint,
                                    '{{ route('zones.zone-points.update', ['zone' => $zone->short_name, 'zonePoint' => $zp->id]) }}',
                                    {
                                        modal: 'zone-point',
                                        resourceName: 'Edit Zone Point'
                                    }
                                )">
                                <x-ui.icon name="edit" />
                            </button>
                            <form action="{{ route('zones.zone-points.destroy', ['zone' => $zone->short_name, 'zonePoint' => $zp->id]) }}"
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
                    <td colspan="6" class="text-center py-6 text-base-content/50">
                        No zone points found.
                    </td>
                </tr>
            @endforelse
        </x-slot:body>
    </x-ui.table>
    <x-slot:footer>
        <div></div>
        <button type="button" class="btn btn-sm btn-soft btn-success"
            @click="$store.modalForm.openCreate({
                baseUrl: '{{ route('zones.zone-points.store', ['zone' => $zone->short_name]) }}',
                resourceName: 'Zone Point',
                modal: 'zone-point',
                defaults: window.zonePointDefaults
        })">
            <x-ui.icon name="add" /> Add
        </button>
    </x-slot:footer>
</x-ui.card>
