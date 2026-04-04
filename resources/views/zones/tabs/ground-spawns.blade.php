<x-ui.card>
    <x-slot:header>
        <h3 class="card-title">Ground Spawns</h3>
        <button type="button" class="btn btn-sm btn-soft btn-success"
            @click="$store.modalForm.openCreate({
                baseUrl: '{{ route('zones.ground-spawns.store', ['zone' => $zone->zoneidnumber]) }}',
                resourceName: 'Ground Spawn',
                modal: 'ground-spawn',
                defaults: {
                    version: {{ $zone->version }},
                    max_x: 2000,
                    max_y: 2000,
                    max_z: 10000,
                    min_x: -2000,
                    min_y: -2000,
                    heading: 0,
                    max_allowed: 1,
                    respawn_timer: 300,
                    fix_z: 1,
                }
        })">
            <x-ui.icon name="add" /> Add
        </button>
    </x-slot:header>
    <x-ui.table>
        <x-slot:head>
            <tr>
                <th scope="col">Item</th>
                <th scope="col" class="w-[5%]">Zone Version</th>
                <th scope="col" class="w-[5%]">Max</th>
                <th scope="col" class="w-[10%]">Max Coords</th>
                <th scope="col" class="w-[10%]">Min Coords</th>
                <th scope="col" class="w-[5%]">Respawn</th>
                <th scope="col" class="w-[10%] text-right">-</th>
            </tr>
        </x-slot:head>
        <x-slot:body>
            @forelse ($zone->groundspawns as $gs)
                <tr x-data data-gs='@json($gs)'>
                    <td>
                        <x-item-link
                            :item_id="$gs->item_->id"
                            :item_name="$gs->item_->Name"
                            :item_icon="$gs->item_->icon"
                            item_class="flex"
                        />
                    </td>
                    <td>{{ $gs->version }}</td>
                    <td>{{ $gs->max_allowed }}</td>
                    <td>
                        x: {{ floor($gs->max_x) }},
                        y: {{ floor($gs->max_y) }},
                        z: {{ floor($gs->max_z) }}
                    </td>
                    <td>
                        x: {{ floor($gs->min_x) }},
                        y: {{ floor($gs->min_y) }}
                    </td>
                    <td>{{ $gs->respawn_timer }}</td>
                    <td class="text-right">
                        <div class="join">
                            <button type="button" class="join-item btn btn-sm btn-soft"
                                @click="$store.modalForm.openEdit(
                                    $el.closest('tr').dataset.gs,
                                    '{{ route('zones.ground-spawns.update', [
                                        'zone' => $zone->zoneidnumber,
                                        'groundspawn' => $gs->id
                                    ]) }}',
                                    {
                                        modal: 'ground-spawn',
                                        resourceName: 'Edit Ground Spawn',
                                    }
                                )">
                                <x-ui.icon name="edit" />
                            </button>
                            <form
                                action="{{ route('zones.ground-spawns.destroy', [
                                    'zone' => $zone->zoneidnumber,
                                    'groundspawn' => $gs->id
                                ]) }}"
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
                        No ground spawns found.
                    </td>
                </tr>
            @endforelse
        </x-slot:body>
    </x-ui.table>
    <x-slot:footer>
        <div></div>
        <button type="button" class="btn btn-sm btn-soft btn-success"
            @click="$store.modalForm.openCreate({
                baseUrl: '{{ route('zones.ground-spawns.store', ['zone' => $zone->zoneidnumber]) }}',
                resourceName: 'Ground Spawn',
                modal: 'ground-spawn',
                defaults: {
                    version: {{ $zone->version }},
                    max_x: 2000,
                    max_y: 2000,
                    max_z: 10000,
                    min_x: -2000,
                    min_y: -2000,
                    heading: 0,
                    max_allowed: 1,
                    respawn_timer: 300,
                    fix_z: 1,
                }
        })">
            <x-ui.icon name="add" /> Add
        </button>
    </x-slot:footer>
</x-ui.card>
