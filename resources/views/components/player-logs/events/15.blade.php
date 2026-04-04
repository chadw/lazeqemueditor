@php
    $d = $log->event_data;

    $currency = null;
    if (!empty($d['alternate_currency_id'])) {
        $currency = $altCurrency->firstWhere('id', $d['alternate_currency_id']);
    }
@endphp
<div class="space-y-0.5 text-sm">
    {{-- Item row --}}
    <div class="flex items-center gap-2">
        <x-item-link
            :item_id="$d['item_id']['item']['id']"
            :item_name="$d['item_id']['item']['Name']"
            :item_icon="$d['item_id']['item']['icon']"
        />

        <span class="badge badge-sm badge-soft badge-accent">
            x{{ $d['charges'] ?? 1 }}
        </span>

        {{-- Cost --}}
        <span class="ml-auto flex items-center gap-1 text-xs">
            @if($currency)
                <x-item-link
                    :item_id="$currency->item->id"
                    :item_name="$currency->item->Name"
                    :item_icon="$currency->item->icon"
                    item_class="scale-90"
                />
                <span class="badge badge-sm badge-soft badge-warning">
                    x{{ $d['cost'] ?? 1 }}
                </span>
            @else
                <span class="text-amber-300">
                    {{ price($d['cost']) }}
                </span>
            @endif
        </span>
    </div>

    {{-- Merchant --}}
    <div class="text-xs text-gray-400">
        Purchased from
        <span class="text-gray-200 font-medium">
            {{ $d['merchant_name'] }}
        </span>
    </div>

    {{-- Currency type --}}
    @if($currency)
        <div class="text-xs text-indigo-300">
            Paid with {{ $currency->item->Name }}
        </div>
    @endif

    {{-- Balance --}}
    <div class="text-xs text-gray-500">
        Balance:
        @if($currency)
            {{ number_format($d['player_currency_balance']) }}
        @else
            {{ price($d['player_money_balance']) }}
        @endif
    </div>
</div>
