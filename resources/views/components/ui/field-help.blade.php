@props(['text'])

<span
    x-data="{ fieldHelpOpen: false }"
    :class="{ 'tooltip-open': fieldHelpOpen }"
    @focus="fieldHelpOpen = true"
    @blur="fieldHelpOpen = false"
    @click.stop.prevent="fieldHelpOpen = true; $el.focus()"
    @keydown.escape.stop="fieldHelpOpen = false; $el.blur()"
    {{ $attributes->merge([
        'class' => 'tooltip tooltip-neutral inline-flex shrink-0 cursor-help align-middle rounded-full text-info/80 hover:text-info focus:text-info focus:outline-none focus:ring-2 focus:ring-info/40',
        'data-tip' => $text,
        'tabindex' => '0',
        'role' => 'note',
        'aria-label' => $text,
        'title' => $text,
    ]) }}
>
    <svg aria-hidden="true" class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none"
        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round"
            d="M9.1 9a3 3 0 1 1 4.83 2.37c-.93.74-1.93 1.31-1.93 2.63m0 4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
    </svg>
</span>
