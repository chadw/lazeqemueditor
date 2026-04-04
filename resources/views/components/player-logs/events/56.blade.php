@php
    $d = $log->event_data;
    $item = data_get($d, 'item_id.item');
    $itemId = data_get($d, 'item_id.id');
@endphp

<div class="space-y-0.5 text-sm">
    <div class="flex items-center gap-2">
        @if ($item)
            <x-item-link
                :item_id="$d['item_id']['item']['id']"
                :item_name="$d['item_id']['item']['Name']"
                :item_icon="$d['item_id']['item']['icon']"
            />
        @else
            <span class="text-gray-500 italic">Unknown Item</span>
        @endif
        <span class="badge badge-sm badge-soft badge-accent">
            x{{ $d['quantity'] ?? 1 }}
        </span>
    </div>
    <div class="text-xs text-gray-400">
        Deposited by
        <span class="font-medium text-gray-200">
            {{ $d['char_id']['character']['name'] }}
        </span>
        Into
        <span class="font-semibold text-gray-200">
            {{ $d['guild_id']['guild']['name'] }}
        </span>
        Guild Bank
    </div>
</div>
