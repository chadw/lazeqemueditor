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
        <span class="ml-auto text-xs text-gray-400">
            {{ price($d['price']) }}
        </span>
    </div>
    <div class="text-xs text-gray-400">
        Sold to
        <span class="text-gray-200 font-medium">
            {{ $d['buyer_name'] }}
        </span>
    </div>
    <div class="text-xs text-emerald-300">
        Total: {{ price($d['total_cost']) }}
    </div>
</div>
