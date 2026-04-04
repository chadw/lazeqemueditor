<x-top-links>
    <button type="button" class="btn btn-soft btn-success"
        @click="$store.modalForm.openCreate({
            baseUrl: '{{ route("factions.characters.store", $character->id) }}',
            resourceName: 'Character Faction',
            modal: 'faction',
            defaults: {
                current_value: 0,
            },
            meta: {
                width: 'max-w-3xl',
            }
        })">
        <x-ui.icon name="add" /> New Faction
    </button>
</x-top-links>

<x-ui.table height="overflow-x-auto max-h-[60vh] overflow-y-auto" theadsticky="top-0 z-10">
    <x-slot:head>
        <tr>
            <th scope="col">Faction</th>
            <th scope="col" class="w-[15%]">Standing</th>
            <th scope="col" class="text-right w-[10%]">Value</th>
            <th scope="col" class="w-[10%]">-</th>
        </tr>
    </x-slot:head>
    <x-slot:body>
        @forelse ($character->faction as $f)
            <tr x-data data-fvalue='@json($f)'>
                <td>
                    <div class="font-medium">{{ optional($f->faction)->name ?? $f->faction_id }}</div>
                    @if (optional($f->faction)->base !== null)
                        <div class="text-xs text-muted">Base: {{ optional($f->faction)->base }}</div>
                    @endif
                </td>
                <td>
                    {!! $f->standing !!}
                </td>
                <td class="text-right">
                    {{ $f->current_value ?? ($f->value ?? 0) }}
                </td>
                <td class="text-right">
                    <div class="join">
                        <button type="button" class="join-item btn btn-sm btn-soft tooltip"
                            data-tip="Edit"
                            @click="$store.modalForm.openEdit(
                                $el.closest('tr').dataset.fvalue,
                                '{{ route('factions.characters.update', [$f->char_id, $f->faction_id]) }}',
                                {
                                    modal: 'faction',
                                    resourceName: 'Edit Character Faction',
                                    width: 'max-w-3xl'
                                }
                            )">
                            <x-ui.icon name="edit" />
                        </button>
                        <form action="{{ route('factions.characters.destroy', [$f->char_id, $f->faction_id]) }}"
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
        @empty
            <tr>
                <td colspan="4" class="text-center py-6 text-base-content/50">
                    No faction data.
                </td>
            </tr>
        @endforelse
    </x-slot:body>
</x-ui.table>
