@php
    $d = $log->event_data;
    $givenItems   = $d['handin_items'] ?? [];
    $returnItems  = $d['return_items'] ?? [];
    $givenMoney   = $d['handin_money'] ?? [];
    $returnMoney  = $d['return_money'] ?? [];

    $hasGiven  = count($givenItems) || array_sum($givenMoney);
    $hasReturn = count($returnItems) || array_sum($returnMoney);
@endphp
<div class="space-y-2 text-sm">
    <div class="text-xs font-semibold">
        NPC Hand-In: <span class="text-secondary">{{ $d['npc_name'] }}</span>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <div class="text-xs uppercase text-gray-400 mb-1">Given</div>
            @forelse ($givenItems as $item)
                <div class="flex items-center gap-2">
                    <x-item-link
                        :item_id="$item['item_id']['item']['id']"
                        :item_name="$item['item_id']['item']['Name']"
                        :item_icon="$item['item_id']['item']['icon']"
                    />
                    <span class="text-xs text-gray-400">
                        ×{{ $item['charges'] ?? 1 }}
                    </span>
                </div>
            @empty
                <div class="text-xs text-gray-500 italic">No items</div>
            @endforelse

            @if(array_sum($givenMoney))
                <div class="text-xs text-gray-400 mt-1">
                    💰 {{ implode(' ', array_filter([
                        $givenMoney['platinum'] ? "{$givenMoney['platinum']}p" : null,
                        $givenMoney['gold'] ? "{$givenMoney['gold']}g" : null,
                        $givenMoney['silver'] ? "{$givenMoney['silver']}s" : null,
                        $givenMoney['copper'] ? "{$givenMoney['copper']}c" : null,
                    ])) }}
                </div>
            @endif
        </div>
        <div>
            <div class="text-xs uppercase text-gray-400 mb-1">Received</div>
            @forelse ($returnItems as $item)
                <div class="flex items-center gap-2">
                    <x-item-link
                        :item_id="$item['item_id']['item']['id']"
                        :item_name="$item['item_id']['item']['Name']"
                        :item_icon="$item['item_id']['item']['icon']"
                    />
                    <span class="text-xs text-gray-400">
                        ×{{ $item['charges'] ?? 1 }}
                    </span>
                </div>
            @empty
                <div class="text-xs text-gray-500 italic">Nothing returned</div>
            @endforelse

            @if(array_sum($returnMoney))
                <div class="text-xs text-gray-400 mt-1">
                    💰 {{ implode(' ', array_filter([
                        $returnMoney['platinum'] ? "{$returnMoney['platinum']}p" : null,
                        $returnMoney['gold'] ? "{$returnMoney['gold']}g" : null,
                        $returnMoney['silver'] ? "{$returnMoney['silver']}s" : null,
                        $returnMoney['copper'] ? "{$returnMoney['copper']}c" : null,
                    ])) }}
                </div>
            @endif
        </div>

    </div>

    @if($d['is_quest_handin'])
        <div class="text-xs text-green-400 font-medium">
            ✔ Quest Hand-In
        </div>
    @endif

</div>
