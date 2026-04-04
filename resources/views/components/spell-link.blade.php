@php
    $spellTargetType = $spellTargetType ?? null;
@endphp

<div x-data class="{{ $spellClass }}" data-target-type="{{ $spellTargetType ?? '' }}">
    <a href="{{ route('spells.edit', $spellId) }}"
        @mouseenter="$store.tooltip.loadTooltip('{{ route('spells.popup', $spellId) }}', $el, $event)"
        @mouseleave="$store.tooltip.hideTooltip()" class="link-info link-hover flex items-center gap-1"
        title="{{ $spellName }}"
        data-effects-only="{{ $effectsOnly ? '1' : '0' }}"
        >

        @if (isset($spellIcon))
            <span class="icon-wrap" aria-hidden="true">
                <span class="spell-icon spell-{{ $spellIcon }} spell-icon-sm rounded-lg {{ config('everquest.spell_target_colors.' . $spellTargetType, '') }}"></span>
            </span>
        @endif

        <span class="whitespace-nowrap">
            {{ $spellName }}
        </span>
        <template x-if="$store.tooltip.loadingUrl === '{{ route('spells.popup', $spellId) }}'">
            <span class="loading loading-spinner loading-xs text-gray-400"></span>
        </template>
    </a>
</div>
