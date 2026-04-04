@php
    if ($type == 'container') {
        $resourceName = 'Container';
    } elseif ($type == 'component') {
        $resourceName = 'Component';
    } elseif ($type == 'success') {
        $resourceName = 'Result';
    }
@endphp
<div class="overflow-x-auto">
    <table class="table table-sm table-zebra">
        <thead>
            <tr>
                <th>Item</th>
                @if ($type === 'component')
                    <th class="w-[10%] text-center">Count</th>
                    <th class="w-[10%] text-center">Returned</th>
                    <th class="w-[10%] text-center">Salvage</th>
                @elseif ($type === 'success')
                    <th class="w-[10%] text-center">Success</th>
                @endif
                <th class="w-[10%] text-right">Actions</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($entries as $entry)
                {{-- @dump($entry) --}}
                <tr x-data data-recipe='@json($entry)'>
                    <td>
                        <div class="flex items-center gap-2">
                            @if ($entry->item)
                                {{ $entry->item_id }}
                                <x-item-link
                                    :item_id="$entry->item->id"
                                    :item_name="$entry->item->Name"
                                    :item_icon="$entry->item->icon"
                                />
                            @elseif (!$entry->item && $entry->container_name)
                                <x-item-link
                                    :item_id="$entry->container_id"
                                    :item_name="$entry->container_name"
                                    :item_icon="$entry->container_icon"
                                />
                            @else
                                <span class="italic opacity-60">
                                    Unknown Item (ID {{ $entry->item_id }})
                                </span>
                            @endif
                        </div>
                    </td>
                    @if ($type === 'component')
                        <td class="text-center">
                            {{ $entry->componentcount }}
                        </td>
                        <td class="text-center">
                            {{ $entry->failcount }}
                        </td>
                        <td class="text-center">
                            {{ $entry->salvagecount }}
                        </td>
                    @elseif ($type === 'success')
                        <td class="text-center">
                            {{ $entry->successcount }}
                        </td>
                    @endif
                    <td class="text-right space-x-1">
                        <div class="join">
                            <button type="button" class="join-item btn btn-sm btn-soft"
                                @click="$store.modalForm.openEdit(
                                {{ $entry->toJson() }},
                                '{{ route('tradeskills.entries.update', $entry) }}',
                                {
                                    modal: 'tradeskill-entries',
                                    title: 'Edit {{ $resourceName }}',
                                    resourceName: '{{ $resourceName }}',
                                }
                            )">
                                <x-ui.icon name="edit" />
                            </button>
                            <form
                                action="{{ route('tradeskills.entries.destroy', $entry) }}"
                                method="POST" class="inline"
                            >
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
                    <td colspan="5" class="text-center italic opacity-60">
                        No entries
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
