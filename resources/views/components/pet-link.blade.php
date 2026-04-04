<div x-data class="{{ $petClass }}">
    <a href="#"
        @mouseenter="$store.tooltip.loadTooltip('{{ route('pets.popup', $petId) }}', $el, $event)"
        @mouseleave="$store.tooltip.hideTooltip()" class="text-base link-info link-hover flex items-center gap-1"
        title="{{ $petName }}"
        data-effects-only="{{ $effectsOnly ? '1' : '0' }}"
        >
        {{ $petName }}
        <template x-if="$store.tooltip.loadingUrl === '{{ route('pets.popup', $petId) }}'">
            <svg class="animate-spin h-3 w-3 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 00-8 8h4z"></path>
            </svg>
        </template>
    </a>
</div>
