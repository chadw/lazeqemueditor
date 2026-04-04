@props([
    'spell_id',
    'spell_name' => '',
    'spell_icon' => null,
    'spell_class' => '',
    'spell_target_type' => null,
    'effects_only' => false,
    'icon_only' => false,
])

<div {{ $attributes->merge(['class' => $spell_class]) }} data-target-type="{{ $spell_target_type ?? '' }}">
    <a href="{{ route('spells.edit', $spell_id) }}"
        @mouseenter="$store.tooltip.loadTooltip('{{ route('spells.popup', $spell_id) }}', $el, $event)"
        @mouseleave="$store.tooltip.hideTooltip()" class="link-info link-hover flex flex-col items-center text-center"
        title="{{ $spell_name }}"
        data-effects-only="{{ $effects_only ? '1' : '0' }}">

        @if (isset($spell_icon))
            <span class="icon-wrap" aria-hidden="true">
                <span class="spell-icon spell-{{ $spell_icon }} spell-icon-sm rounded-lg {{ config('everquest.spell_target_colors.' . $spell_target_type, '') }}"></span>
                <template x-if="$store.tooltip.loadingUrl === '{{ route('spells.popup', $spell_id) }}'">
                    <span class="absolute loading loading-spinner loading-xs text-gray-400"></span>
                </template>
            </span>
        @endif

        @unless($icon_only)
            <span class="text-xs truncate max-w-28 sm:max-w-28 lg:max-w-32 pt-2">
                {!! html_entity_decode($spell_name, ENT_QUOTES, 'UTF-8') !!}
            </span>
        @endunless
    </a>
</div>
