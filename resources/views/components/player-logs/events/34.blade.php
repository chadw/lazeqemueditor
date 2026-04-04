@php
    $d = $log->event_data;
    $item = data_get($d, 'item_id.item');
    $itemId = data_get($d, 'item_id.id');
@endphp
<div class="flex flex-col gap-1">
    <div class="flex items-center gap-2">
        @if ($item)
            <x-item-link
                :item_id="$d['item_id']['item']['id']"
                :item_name="$d['item_id']['item']['Name']"
                :item_icon="$d['item_id']['item']['icon']"
                item_class="flex-inline"
            />
        @else
            <span class="text-gray-500 italic">Unknown Item</span>
        @endif
        <span class="text-sm font-medium text-base-content/80">
            <span class="badge badge-sm badge-soft badge-accent">
                x{{ $log->event_data['charges'] ?? 1 }}
            </span>
            - to slot {{ $log->event_data['slot_id'] }}
        </span>
    </div>
</div>
