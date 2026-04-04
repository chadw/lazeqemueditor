@php
    $d = $log->event_data;
@endphp

<div class="space-y-0.5 text-sm">
    {{-- Item + Quantity --}}
    <div class="flex items-center gap-2">
        @if(!empty($d['item_id']['item']))
            <x-item-link
                :item_id="$d['item_id']['item']['id']"
                :item_name="$d['item_id']['item']['Name']"
                :item_icon="$d['item_id']['item']['icon']"
            />
        @endif

        <span class="badge badge-sm badge-soft badge-accent">
            x{{ $d['quantity'] ?? 1 }}
        </span>
    </div>

    {{-- From Player --}}
    <div class="text-xs text-gray-400">
        Sent to
        <span class="text-gray-200 font-medium">
            {{ $d['to_player_name'] ?? 'Unknown' }}
        </span>
        on
        <span class="text-gray-200 font-medium">
            {{ \Carbon\Carbon::createFromTimestamp($d['sent_date'])->format('M d, Y H:i') }}
        </span>
    </div>

    {{-- Aug slots if any --}}
    @php
        $augSlots = collect([
            $d['aug_slot_1'] ?? 0,
            $d['aug_slot_2'] ?? 0,
            $d['aug_slot_3'] ?? 0,
            $d['aug_slot_4'] ?? 0,
            $d['aug_slot_5'] ?? 0,
            $d['aug_slot_6'] ?? 0,
        ])->filter(fn($val) => $val > 0);
    @endphp

    @if($augSlots->isNotEmpty())
        <div class="flex items-center gap-1 text-xs text-gray-400">
            <span>Augments:</span>
            @foreach($augSlots as $slot)
                <span class="px-1 py-0.5 bg-gray-800 rounded text-gray-200 text-[0.65rem]">{{ $slot }}</span>
            @endforeach
        </div>
    @endif
</div>
