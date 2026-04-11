@foreach ($lt->loottableEntries as $entry)
    <div class="card bg-base-100 shadow-sm border border-base-300 overflow-hidden"
        x-data
        data-loot='@json([
            "entry" => $entry->getAttributes(),
            "lootdrop" => $entry->lootdrop,
        ])'
    >
        <div class="bg-neutral text-neutral-content px-4 py-3 flex flex-wrap justify-between items-center gap-4 border-b border-base-300">
            <div class="flex items-center gap-3">
                <div class="badge badge-soft badge-info font-mono text-xs">{{ $entry->lootdrop?->name ?? 'LootDrop #' . $entry->lootdrop_id }}</div>
                <h3 class="font-bold text-sm">
                    <div class="text-xs opacity-60 font-mono">
                        LootDrop ID: {{ $entry->lootdrop_id }}
                        · Prob: {{ $entry->probability }}%
                        · Limit: {{ $entry->droplimit ?? '—' }}
                        · Min: {{ $entry->mindrop ?? '—' }}
                    </div>
                </h3>
            </div>
            <div class="flex gap-4 items-center text-xs uppercase tracking-wider font-bold opacity-80">
                <div class="join">
                    <button
                        type="button"
                        class="join-item btn btn-sm btn-soft tooltip"
                        data-tip="Edit Loot Drop"
                        @click="
                            $store.modalForm.openEdit(
                                $el.closest('[data-loot]').dataset.loot,
                                '{{ route('loot.entries.update', [
                                    'loottable' => $lt->id,
                                    'lootdrop' => $entry->lootdrop_id
                                ]) }}',
                                {
                                    resourceName: 'Edit Loot Drop',
                                    modal: 'lootdrop'
                                }
                        )">
                        <x-ui.icon name="edit" /> Edit Loot Drop
                    </button>
                    <form method="POST"
                        action="{{ route('loot.entries.destroy', [
                            'loottable' => $lt->id,
                            'lootdrop' => $entry->lootdrop_id,
                        ]) }}">
                        @csrf
                        @method('DELETE')
                        <button class="join-item btn btn-sm btn-soft btn-error tooltip"
                            data-tip="Delete"
                            onclick="return confirm('Remove this loot drop from the table?')">
                            <x-ui.icon name="delete" />
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="table table-zebra table-sm w-full">
                <thead class="bg-base-300/50">
                    <tr>
                        <th class="w-[5%]">Item ID</th>
                        <th>Item Name</th>
                        <th class="w-[10%]">Chance</th>
                        <th class="w-[10%]">Charges</th>
                        <th class="w-[10%]">Equip</th>
                        <th class="w-[15%] text-right">-</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($entry->lootdropEntries as $dropEntry)
                        <tr x-data data-dropentry='@json($dropEntry)'>
                            <td>
                                {{ $dropEntry->item->id }}
                            </td>
                            <td>
                                @if ($dropEntry->item)
                                <x-item-link
                                    :item_id="$dropEntry->item->id"
                                    :item_name="$dropEntry->item->Name"
                                    :item_icon="$dropEntry->item->icon"
                                    item_class="flex"
                                />
                                @else
                                    <span class="italic opacity-60">Item #{{ $dropEntry->item_id }} (not found)</span>
                                @endif
                            </td>

                            <td>{{ $dropEntry->chance }}%</td>
                            <td>{{ $dropEntry->item_charges ?? '—' }}</td>
                            <td>
                                @if ($dropEntry->equip_item)
                                    <span class="badge badge-sm badge-soft badge-success">Yes</span>
                                @else
                                    <span class="badge badge-sm badge-soft badge-error">No</span>
                                @endif
                            </td>

                            <td class="text-right">
                                <div class="join">
                                    <button type="button" class="join-item btn btn-sm btn-soft tooltip"
                                        data-tip="Edit Loot Drop Item"
                                        @click="$store.modalForm.openEdit(
                                        $el.closest('tr').dataset.dropentry,
                                        '{{ route('loot.drops.entries.update', [
                                            'drop' => $entry->lootdrop_id,
                                            'item' => $dropEntry->item_id,
                                        ]) }}',
                                        {
                                            modal: 'lootdrop-items',
                                            resourceName: 'Edit Loot Drop Item'
                                        }
                                    )">
                                        <x-ui.icon name="edit" />
                                    </button>
                                    <form method="POST"
                                        action="{{ route('loot.drops.entries.destroy', [
                                            'drop' => $entry->lootdrop_id,
                                            'item' => $dropEntry->item_id,
                                        ]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="join-item btn btn-sm btn-soft btn-error tooltip"
                                            data-tip="Delete"
                                            onclick="return confirm('Remove item from loot drop?')">
                                            <x-ui.icon name="delete" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                    @if ($entry->lootdropEntries->isEmpty())
                        <tr>
                            <td colspan="6" class="text-center opacity-60">
                                No items in this loot drop
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        {{-- Add Item --}}
        @php
            $totalChance = $entry->lootdropEntries->sum('chance');
            $percent = min($totalChance, 100);
        @endphp
        <div class="card-actions justify-between p-2 bg-base-300/20 border-t border-base-300">
            <div class="flex items-center gap-3 text-xs font-semibold">

                <div class="flex items-center gap-2">
                    <span class="opacity-70 uppercase tracking-wider">Total Chance</span>
                    <div class="w-32 bg-base-200 rounded-full h-2 overflow-hidden">
                        <div
                            class="h-full transition-all duration-300
                                {{ $totalChance == 100
                                    ? 'bg-success'
                                    : ($totalChance > 100 ? 'bg-error' : 'bg-warning') }}"
                            style="width: {{ $percent }}%"
                        ></div>
                    </div>
                </div>
                <div class="badge badge-sm badge-soft
                    {{ $totalChance == 100
                        ? 'badge-success'
                        : ($totalChance > 100 ? 'badge-error' : 'badge-warning') }}">
                    {{ $totalChance }} / 100%
                </div>

            </div>
            <div class="flex gap-2 items-center">
                <button type="button" class="btn btn-xs btn-soft btn-success"
                    @click="$store.modalForm.openCreate({
                        baseUrl: '{{ route('loot.drops.entries.store', $entry->lootdrop_id) }}',
                        resourceName: 'Loot Item',
                        modal: 'lootdrop-items',
                    })">
                    <x-ui.icon name="add" /> Add Item
                </button>
            </div>
        </div>

    </div>
@endforeach

@if ($lt->loottableEntries->isEmpty())
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body p-4">
            <div class="flex items-center justify-between gap-6">
                <h2 class="card-title whitespace-nowrap">
                    Attach LootDrop
                </h2>
                <div class="flex items-center gap-6">
                    <span class="">Attach an existing loot drop</span>
                    <div class="flex items-center gap-2">
                        @include('loot.partials.lootdrop-selector', [
                            'lt' => $lt,
                        ])
                    </div>
                    <div class="divider divider-horizontal m-0 px-2">
                        OR
                    </div>
                    <button type="button" class="btn btn-sm btn-soft btn-success"
                        @click="$store.modalForm.openCreate({
                        baseUrl: '{{ route('loot.entries.store', $lt) }}',
                        resourceName: 'Loot Table Entry',
                        modal: 'lootdrop',
                        defaults: {
                            multiplier: 1,
                            droplimit: 0,
                            mindrop: 0,
                            probability: 100,
                        }
                    })">
                        <x-ui.icon name="add" /> Create New Loot Drop
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
