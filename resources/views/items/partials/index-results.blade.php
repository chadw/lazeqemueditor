<div class="space-y-6">
    <x-ui.table>
        <x-slot:head>
            <tr>
                <th scope="col" class="w-[5%]">ID</th>
                <th scope="col">Name</th>
                <th scope="col" class="w-[10%]">Type</th>
                <th scope="col" class="w-[10%]">Updated</th>
                <th scope="col" class="w-[15%] text-right">-</th>
            </tr>
        </x-slot:head>
        <x-slot:body>
            @foreach ($items as $item)
                <tr>
                    <td scope="row">
                        {{ $item->id }}
                    </td>
                    <td>
                        <div class="flex flex-col">
                            <x-item-link
                                :item_id="$item->id"
                                :item_name="$item->Name"
                                :item_icon="$item->icon"
                                item_class="flex"
                            />
                            <span class="text-xs uppercase text-gray-500 ml-11 truncate">
                                @if ($item->slots > 0)
                                    {{ get_slots_string($item->slots) }}
                                @endif
                                @if ($item->bagslots > 0)
                                    <strong>Slots:</strong> {{ $item->bagslots }}
                                    @if ($item->bagwr > 0)
                                        <strong>WR:</strong> {{ $item->bagwr }}%
                                    @endif
                                @endif
                            </span>
                        </div>
                    </td>
                    <td>
                        {{ config('everquest.item_types.' . $item->itemtype) }}
                    </td>
                    <td class="hidden md:table-cell text-nowrap">
                        {{ \Carbon\Carbon::parse($item->updated)->format('Y-m-d H:i') }}
                    </td>
                    <td class="text-right">
                        <div class="inline join">
                            <form method="POST" action="{{ route('items.clone', $item) }}" class="inline-block">
                                @csrf
                                <input type="hidden" name="redirect" value="edit" />
                                <button type="submit" class="btn btn-sm btn-soft btn-info tooltip" data-tip="Clone">
                                    <x-ui.icon name="clone" />
                                </button>
                            </form>
                            <a href="{{ route('items.edit', $item) }}"
                                class="join-item btn btn-sm btn-soft tooltip" data-tip="Edit">
                                <x-ui.icon name="edit" />
                            </a>
                            <form action="{{ route('items.destroy', $item) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button class="join-item btn btn-sm btn-soft btn-error tooltip" data-tip="Delete"
                                    onclick="return confirm('Delete?')">
                                    <x-ui.icon name="delete" />
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-slot:body>
    </x-ui.table>

    @if ($items instanceof \Illuminate\Contracts\Pagination\Paginator)
        <div class="mt-4 shrink-0">{{ $items->links() }}</div>
    @endif

</div>
