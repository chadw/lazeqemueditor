@php
    $d = $log->event_data;

    $left = [
        'name'  => $d['character_1_name'],
        'items' => $d['character_1_give_items'] ?? [],
        'money' => $d['character_1_give_money'] ?? [],
    ];

    $right = [
        'name'  => $d['character_2_name'],
        'items' => $d['character_2_give_items'] ?? [],
        'money' => $d['character_2_give_money'] ?? [],
    ];
@endphp
<div class="flex items-start gap-3 text-sm">

    <div class="min-w-45 text-right">
        <div class="font-medium text-gray-300">
            {{ $left['name'] }}
        </div>

        @forelse($left['items'] as $item)
            <div class="flex justify-end items-center gap-1 mt-0.5">
                <x-item-link
                    :item_id="$item['item_id']['item']['id']"
                    :item_name="$item['item_id']['item']['Name']"
                    :item_icon="$item['item_id']['item']['icon']"
                />
                <span class="badge badge-sm badge-soft badge-accent">
                    x{{ $item['charges'] ?? 1 }}
                </span>
            </div>
        @empty
            <div class="text-xs italic text-gray-500 mt-0.5">
                nothing
            </div>
        @endforelse

        @if(array_sum($left['money']) > 0)
            <div class="text-xs text-gray-400 mt-0.5">
                💰 {{ implode(' ', array_filter([
                    $left['money']['platinum'] ? "{$left['money']['platinum']}pp" : null,
                    $left['money']['gold'] ? "{$left['money']['gold']}gp" : null,
                    $left['money']['silver'] ? "{$left['money']['silver']}sp" : null,
                    $left['money']['copper'] ? "{$left['money']['copper']}cp" : null,
                ])) }}
            </div>
        @endif
    </div>

    <div class="flex items-center pt-4 text-gray-400 text-lg">
        ⇄
    </div>

    <div class="min-w-45">
        <div class="font-medium text-gray-300">
            {{ $right['name'] }}
        </div>

        @forelse($right['items'] as $item)
            <div class="flex items-center gap-1 mt-0.5">
                <x-item-link
                    :item_id="$item['item_id']['item']['id']"
                    :item_name="$item['item_id']['item']['Name']"
                    :item_icon="$item['item_id']['item']['icon']"
                />
                <span class="badge badge-sm badge-soft badge-accent">
                    x{{ $item['charges'] ?? 1 }}
                </span>
            </div>
        @empty
            <div class="text-xs italic text-gray-500 mt-0.5">
                nothing
            </div>
        @endforelse

        @if(array_sum($right['money']) > 0)
            <div class="text-xs text-gray-400 mt-0.5">
                💰 {{ implode(' ', array_filter([
                    $right['money']['platinum'] ? "{$right['money']['platinum']}pp" : null,
                    $right['money']['gold'] ? "{$right['money']['gold']}gp" : null,
                    $right['money']['silver'] ? "{$right['money']['silver']}sp" : null,
                    $right['money']['copper'] ? "{$right['money']['copper']}cp" : null,
                ])) }}
            </div>
        @endif
    </div>
</div>
