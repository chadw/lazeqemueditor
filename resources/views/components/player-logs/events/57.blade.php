@php
    $item = $log->event_data['item_id']['item'] ?? null;
    $character = $log->event_data['char_id']['character'] ?? null;
    $guild = $log->event_data['guild_id']['guild'] ?? null;
@endphp

<div class="flex flex-col gap-0.5 text-sm">
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
            x{{ $log->event_data['quantity'] ?? 1 }}
        </span>
    </div>

    {{-- Character → Guild --}}
    <div class="text-xs text-gray-400 flex items-center gap-1">
        @if($character)
            <span class="text-gray-300">{{ $character['name'] }}</span>
        @else
            <span class="italic">Unknown</span>
        @endif

        <span>→</span>

        @if($guild)
            <span class="text-gray-300">{{ $guild['name'] }}</span>
        @else
            <span class="italic">No Guild</span>
        @endif
    </div>
</div>
