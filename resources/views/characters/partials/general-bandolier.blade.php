@php
    $slotNames = [
        0 => 'Primary',
        1 => 'Secondary',
        2 => 'Range',
        3 => 'Ammo',
    ];
    $bandGroups = $character->bandolier->groupBy('bandolier_id');
@endphp
<div class="bg-base-200 p-4 rounded mt-4">
    <div class="grid grid-cols-1 gap-4">
        <div class="text-lg font-medium">Bandoliers
            <span class="badge badge-sm badge-soft badge-accent ml-2">{{ $character->bandolier->unique('bandolier_id')->count() }}</span>
        </div>

        <div class="grid grid-cols-2 gap-4">
            @foreach ($bandGroups as $bId => $items)
                @php
                    $name = optional($items->first())->bandolier_name ?: 'Bandolier '.$bId;
                @endphp
                <div class="p-4 bg-base-100 rounded-lg">
                    <div class="flex items-center justify-between mb-3">
                        <div class="font-semibold">{{ $name }}</div>
                    </div>
                    <div class="grid grid-cols-4 gap-3">
                        @foreach ($slotNames as $slotIndex => $slotLabel)
                            @php
                                $entry = $items->firstWhere('bandolier_slot', $slotIndex);
                                $item = $entry && $entry->item ? $entry->item : null;
                            @endphp
                            <div class="p-3 bg-base-200/50 rounded text-center flex flex-col items-center">
                                <div class="text-xs text-muted mb-2">{{ $slotLabel }}</div>
                                    @if ($item)
                                        <a href="{{ route('items.edit', $item->id) }}"
                                        @mouseenter="$store.tooltip.loadTooltip('{{ route('items.popup', $item->id) }}', $el, $event)"
                                        @mouseleave="$store.tooltip.hideTooltip()"
                                        class="w-10 h-10 flex items-center justify-center rounded border border-base-content/10 bg-base-100"
                                        title="{{ $item->Name ?? $item->name ?? 'Item' }}"
                                        aria-label="{{ $item->Name ?? $item->name ?? 'Item' }}">
                                            <span class="item-icon item-{{ $item->icon }} item-icon-sm"></span>
                                        </a>
                                    @else
                                        <div class="w-10 h-10 flex items-center justify-center bg-base-200 border border-base-content/20 rounded mb-1">
                                            <svg class="h-4 w-4 text-base-content/40" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/></svg>
                                        </div>
                                    @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
