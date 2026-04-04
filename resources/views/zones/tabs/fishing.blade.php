<x-ui.card>
    <x-slot:header>
        <h3 class="card-title">Fishing</h3>
        <button type="button" class="btn btn-sm btn-soft btn-success"
            @click="$store.modalForm.openCreate({
                baseUrl: '{{ route('zones.fishing.store', ['zone' => $zone->zoneidnumber]) }}',
                resourceName: 'Fish',
                modal: 'fishing',
                defaults: {
                    skill_level: 0,
                    chance: 50,
                    npc_id: 0,
                    npc_chance: 0,
                }
        })">
            <x-ui.icon name="add" /> Add
        </button>
    </x-slot:header>
    <x-ui.table>
        <x-slot:head>
            <tr>
                <th scope="col">Item</th>
                <th scope="col" class="w-[5%]">Skill Level</th>
                <th scope="col" class="w-[5%]">Chance</th>
                <th scope="col" class="w-[10%] text-right">-</th>
            </tr>
        </x-slot:head>
        <x-slot:body>
            @forelse ($fishing as $fish)
                <tr x-data data-fish='@json($fish)'>
                    <td>
                        <x-item-link
                            :item_id="$fish->item->id"
                            :item_name="$fish->item->Name"
                            :item_icon="$fish->item->icon"
                            item_class="flex"
                        />
                    </td>
                    <td>{{ $fish->skill_level }}</td>
                    <td>{{ $fish->chance }}%</td>
                    <td class="text-right">
                        <div class="join">
                            <button type="button" class="join-item btn btn-sm btn-soft"
                                @click="$store.modalForm.openEdit(
                                    $el.closest('tr').dataset.fish,
                                    '{{ route('zones.fishing.update', ['zone' => $zone->zoneidnumber, 'fish' => $fish->id]) }}',
                                    {
                                        modal: 'fishing',
                                        resourceName: 'Edit Fishing',
                                    }
                                )">
                                <x-ui.icon name="edit" />
                            </button>
                            <form
                                action="{{ route('zones.fishing.destroy', ['zone' => $zone->zoneidnumber, 'fish' => $fish->id]) }}"
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
                    <td colspan="4" class="text-center py-6 text-base-content/50">
                        No fishable items found.
                    </td>
                </tr>
            @endforelse
        </x-slot:body>
    </x-ui.table>
    <x-slot:footer>
        <div></div>
        <button type="button" class="btn btn-sm btn-soft btn-success"
            @click="$store.modalForm.openCreate({
                baseUrl: '{{ route('zones.fishing.store', ['zone' => $zone->zoneidnumber]) }}',
                resourceName: 'Fish',
                modal: 'fishing',
                defaults: {
                    skill_level: 0,
                    chance: 50,
                    npc_id: 0,
                    npc_chance: 0,
                }
        })">
            <x-ui.icon name="add" /> Add
        </button>
    </x-slot:footer>
</x-ui.card>
