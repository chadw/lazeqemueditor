@php
    $d = $log->event_data;
@endphp

<div class="space-y-0.5 text-sm">
    {{-- Item + Quantity + Total Cost --}}
    <div class="flex items-center gap-2">
        @if(!empty($d['item_id']['item']))
            <x-item-link
                :item_id="$d['item_id']['item']['id']"
                :item_name="$d['item_id']['item']['Name']"
                :item_icon="$d['item_id']['item']['icon']"
            />
        @endif

        <span class="badge badge-sm badge-soft badge-accent">
            x{{ $d['item_quantity'] ?? 1 }}
        </span>

        <span class="ml-auto text-xs text-amber-300">
            {{ price($d['total_cost'] ?? 0) }}
        </span>
    </div>

    {{-- Buyer / Seller --}}
    <div class="text-xs text-gray-400">
        <span class="text-gray-200 font-medium">
            {{ $d['buyer_name'] }}
        </span>
        bought from
        <span class="text-gray-200 font-medium">
            {{ $d['seller_name'] }}
        </span>
    </div>

    {{-- Status --}}
    @if(!empty($d['status']))
        <div class="text-xs text-emerald-400">
            {{ $d['status'] }}
        </div>
    @endif

    {{-- Trade items (future-proof) --}}
    @if(!empty($d['trade_items']))
        <div class="text-xs text-gray-500">
            Additional trade items involved
        </div>
    @endif
</div>
