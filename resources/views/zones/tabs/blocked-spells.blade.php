<div class="mb-4">
    <x-ui.alert-info>
        <p>
            Blocked spells will prevent these spells from landing on players in ALL versions of the zone.
        </p>
    </x-ui.alert-info>
</div>

<x-ui.card>
    <x-slot:header>
        <h3 class="card-title">Blocked Spells</h3>
        <button type="button" class="btn btn-sm btn-soft btn-success"
            @click="$store.modalForm.openCreate({
                baseUrl: '{{ route('zones.blocked-spells.store', ['zone' => $zone->zoneidnumber]) }}',
                resourceName: 'Blocked Spell',
                modal: 'blocked-spell',
                defaults: {
                    x: 0,
                    y: 0,
                    z: 0,
                    x_diff: 0,
                    y_diff: 0,
                    z_diff: 0,
                }
        })">
            <x-ui.icon name="add" /> Add
        </button>
    </x-slot:header>
    <x-ui.table>
        <x-slot:head>
            <tr>
                <th scope="col">Spell</th>
                <th scope="col" class="w-[10%]">Type</th>
                <th scope="col">Message</th>
                <th scope="col">Description</th>
                <th scope="col" class="w-[10%] text-right">-</th>
            </tr>
        </x-slot:head>
        <x-slot:body>
            @forelse ($zone->blockedSpells as $bs)
                <tr x-data data-blockedspell='@json($bs)'>
                    <td>
                        @if ($bs->spell)
                            <x-spell-link
                                :spell_id="$bs->spell->id"
                                :spell_name="$bs->spell->name"
                                :spell_icon="$bs->spell->new_icon"
                                spell_class="inline-flex"
                                :effects_only="1"
                            />
                        @else
                            Unknown (ID: {{ $bs->spellid }})
                        @endif
                    </td>
                    <td>{{ config('everquest.blocked_spell_type.' . $bs->type) }}</td>
                    <td class="truncate">{{ $bs->message }}</td>
                    <td class="truncate">{{ $bs->description }}</td>
                    <td class="text-right">
                        <div class="join">
                            <button type="button" class="join-item btn btn-sm btn-soft"
                                @click="$store.modalForm.openEdit(
                                    $el.closest('tr').dataset.blockedspell,
                                    '{{ route('zones.blocked-spells.update', [$zone->zoneidnumber, $bs->id]) }}',
                                    {
                                        modal: 'blocked-spell',
                                        resourceName: 'Edit Blocked Spell'
                                    }
                                )">
                                <x-ui.icon name="edit" />
                            </button>
                            <form
                                action="{{ route('zones.blocked-spells.destroy', [$zone->zoneidnumber, $bs->id]) }}"
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
                    <td colspan="5" class="text-center py-6 text-base-content/50">
                        No blocked spells found.
                    </td>
                </tr>
            @endforelse
        </x-slot:body>
    </x-ui.table>
    <x-slot:footer>
        <div></div>
        <button type="button" class="btn btn-sm btn-soft btn-success"
            @click="$store.modalForm.openCreate({
                baseUrl: '{{ route('zones.blocked-spells.store', ['zone' => $zone->zoneidnumber]) }}',
                resourceName: 'Blocked Spell',
                modal: 'blocked-spell',
                defaults: {
                    x: 0,
                    y: 0,
                    z: 0,
                    x_diff: 0,
                    y_diff: 0,
                    z_diff: 0,
                }
        })">
            <x-ui.icon name="add" /> Add
        </button>
    </x-slot:footer>
</x-ui.card>
