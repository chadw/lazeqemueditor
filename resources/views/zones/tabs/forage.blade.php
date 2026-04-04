<x-ui.card>
    <x-slot:header>
        <h3 class="card-title">Forage</h3>
        <button type="button" class="btn btn-sm btn-soft btn-success"
            @click="$store.modalForm.openCreate({
                baseUrl: '{{ route('zones.forage.store', ['zone' => $zone->zoneidnumber]) }}',
                resourceName: 'Forage',
                modal: 'forage',
                defaults: {
                    level: 0,
                    chance: 50,
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
            @forelse ($forages as $forage)
                <tr x-data data-forage='@json($forage)'>
                    <td>
                        @if ($forage->item)
                            <x-item-link
                                :item_id="$forage->item->id"
                                :item_name="$forage->item->Name"
                                :item_icon="$forage->item->icon"
                                item_class="flex"
                            />
                        @endif
                    </td>
                    <td>{{ $forage->level }}</td>
                    <td>{{ $forage->chance }}%</td>
                    <td class="text-right">
                        <div class="join">
                            <button type="button" class="join-item btn btn-sm btn-soft"
                                @click="$store.modalForm.openEdit(
                                    $el.closest('tr').dataset.forage,
                                    '{{ route('zones.forage.update', ['zone' => $zone->zoneidnumber, 'forage' => $forage->id]) }}',
                                    {
                                        modal: 'forage',
                                        resourceName: 'Edit Forage',
                                    }
                                )">
                                <x-ui.icon name="edit" />
                            </button>
                            <form
                                action="{{ route('zones.forage.destroy', ['zone' => $zone->zoneidnumber, 'forage' => $forage->id]) }}"
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
                        No foragable items found.
                    </td>
                </tr>
            @endforelse
        </x-slot:body>
    </x-ui.table>
    <x-slot:footer>
        <div></div>
        <button type="button" class="btn btn-sm btn-soft btn-success"
            @click="$store.modalForm.openCreate({
                baseUrl: '{{ route('zones.forage.store', ['zone' => $zone->zoneidnumber]) }}',
                resourceName: 'Forage',
                modal: 'forage',
                defaults: {
                    level: 0,
                    chance: 50,
                }
        })">
            <x-ui.icon name="add" /> Add
        </button>
    </x-slot:footer>
</x-ui.card>
