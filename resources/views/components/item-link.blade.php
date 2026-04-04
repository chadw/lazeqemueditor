<div x-data class="{{ $itemClass }}">
    <a href="{{ route('items.edit', $itemId) }}"
        @mouseenter="$store.tooltip.loadTooltip('{{ route('items.popup', $itemId) }}', $el, $event)"
        @mouseleave="$store.tooltip.hideTooltip()" class="text-base link-info link-hover flex items-center gap-1"
        title="{{ $itemName }}">

        @if (isset($itemIcon))
            <span class="icon-wrap" aria-hidden="true">
                <span class="item-icon item-{{ $itemIcon }} item-icon-sm"></span>
            </span>
        @endif

        <span class="whitespace-nowrap">
            {{ $itemName }}
        </span>

        <template x-if="$store.tooltip.loadingUrl === '{{ route('items.popup', $itemId) }}'">
            <span class="loading loading-spinner loading-xs text-gray-400"></span>
        </template>
    </a>
</div>
