@php
    $nextDoorId = (($zone->doors()->max('doorid')) ?? 0) + 1;
    $zoneDoorDefaults = [
        'zone' => $zone->short_name,
        'version' => $zone->version,
        'doorid' => $nextDoorId,
        'pos_x' => 0,
        'pos_y' => 0,
        'pos_z' => 0,
        'heading' => 256,
        'size' => 100,
        'incline' => 0,
        'door_param' => 0,
        'client_version_mask' => 4294967295,
        'dest_instance' => 0,
        'dest_x' => 0,
        'dest_y' => 0,
        'dest_z' => 0,
        'dest_heading' => 0,
        'invert_state' => 0,
        'dz_switch_id' => 0,
        'guild' => 0,
        'lockpick' => 0,
        'triggerdoor' => 0,
        'triggertype' => 0,
        'close_timer_ms' => 5000,
        'buffer' => 0,
    ];
@endphp
@push('scripts')
<script>
    window.zoneDoorDefaults = @json($zoneDoorDefaults);
</script>
@endpush

<x-ui.card>
    <x-slot:header>
        <h3 class="card-title">Doors</h3>
        <button type="button" class="btn btn-sm btn-soft btn-success"
            @click="$store.modalForm.openCreate({
                baseUrl: '{{ route('zones.doors.store', ['zone' => $zone->short_name]) }}',
                resourceName: 'Door',
                modal: 'door',
                defaults: window.zoneDoorDefaults
        })">
            <x-ui.icon name="add" /> Add
        </button>
    </x-slot:header>
    <x-ui.table>
        <x-slot:head>
            <tr>
                <th scope="col" class="w-[5%]">DoorID</th>
                <th scope="col">Zone</th>
                <th scope="col" class="w-[5%]">Version</th>
                <th scope="col" class="w-[10%]">Name</th>
                <th scope="col" class="w-[20%]">Coords</th>
                <th scope="col">Dest Zone</th>
                <th scope="col">Open Type</th>
                <th scope="col" class="w-[10%] text-right">-</th>
            </tr>
        </x-slot:head>
        <x-slot:body>
            @forelse ($zone->doors as $door)
                <tr x-data data-door='@json($door)'>
                    <td>{{ $door->doorid }}</td>
                    <td>{{ Str::lower($door->zone) }}</td>
                    <td>{{ $door->version }}</td>
                    <td>{{ $door->name }}</td>
                    <td>
                        x: {{ floor($door->pos_x) }},
                        y: {{ floor($door->pos_y) }},
                        z: {{ floor($door->pos_z) }},
                        heading: {{ floor($door->heading) }}
                    </td>
                    <td>{{ $door->dest_zone }}</td>
                    <td>{{ config('everquest.door_open_type.' . $door->opentype) }}</td>
                    <td class="text-right">
                        <div class="join">
                            <button type="button" class="join-item btn btn-sm btn-soft"
                                @click="$store.modalForm.openEdit(
                                    $el.closest('tr').dataset.door,
                                    '{{ route('zones.doors.update', ['zone' => $zone->short_name, 'door' => $door->id]) }}',
                                    {
                                        modal: 'door',
                                        resourceName: 'Edit Door'
                                    }
                                )">
                                <x-ui.icon name="edit" />
                            </button>
                            <form
                                action="{{ route('zones.doors.destroy', ['zone' => $zone->short_name, 'door' => $door->id]) }}"
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
                    <td colspan="8" class="text-center py-6 text-base-content/50">
                        No door objects found.
                    </td>
                </tr>
            @endforelse
        </x-slot:body>
    </x-ui.table>
    <x-slot:footer>
        <div></div>
        <button type="button" class="btn btn-sm btn-soft btn-success"
            @click="$store.modalForm.openCreate({
                baseUrl: '{{ route('zones.doors.store', ['zone' => $zone->short_name]) }}',
                resourceName: 'Door',
                modal: 'door',
                defaults: window.zoneDoorDefaults
        })">
            <x-ui.icon name="add" /> Add
        </button>
    </x-slot:footer>
</x-ui.card>
