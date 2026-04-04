{{-- @dump($log->event_type_id, $log->event_data) --}}
@php
    $item = $log->event_data['item_id']['item'] ?? null;
    $altCurrency = $log->event_data['alternate_currency_id'] ?? 0;
    $merchant = $log->event_data['merchant_name'] ?? 'Unknown Merchant';
@endphp
<div class="flex flex-col gap-0.5 text-sm">
    <div class="flex items-center justify-between gap-2">
        <div class="flex items-center gap-1">
            @if($item)
                <x-item-link
                    :item_id="$item['id']"
                    :item_name="$item['Name']"
                    :item_icon="$item['icon']"
                    item_class="inline-flex"
                />
            @endif
            <span class="badge badge-sm badge-soft badge-accent">
                x{{ $log->event_data['charges'] ?? 1 }}
            </span>
        </div>
        @if($log->event_data['cost'])
            <div class="text-xs font-medium text-amber-400 whitespace-nowrap">
                @if($altCurrency > 0)
                    {{ number_format($log->event_data['cost']) }}
                    <span class="text-[10px] text-gray-400">Alt</span>
                @else
                    {{ price($log->event_data['cost']) }}
                @endif
            </div>
        @endif
    </div>

    <div class="text-xs text-gray-400">
        Merchant: <span class="text-gray-300">{{ $merchant }}</span>
    </div>
</div>
