@props([
    'title',
    'active' => [],
    'section',
])

@php
    $isActive = !empty($active) ? request()->routeIs($active) : false;
@endphp

<div
    x-cloak
    x-data
    x-init="$store.sidebar.init(@js($isActive ? $section : null))"
    class="border-b border-base-300"
>
    <button
        @click="$store.sidebar.toggle('{{ $section }}')"
        class="cursor-pointer w-full flex items-center justify-between px-4 py-3 text-xs uppercase
            tracking-wide transition-all duration-200"
        :class="$store.sidebar.isOpen('{{ $section }}')
            ? 'text-info font-bold bg-base-200'
            : 'text-gray-400 hover:text-white hover:bg-base-200/40'"
    >
        <span class="flex items-center gap-3">
            {{ $icon ?? '' }}
            <span>{{ $title }}</span>
        </span>
        <svg
            class="w-4 h-4 transition-transform duration-200"
            :class="$store.sidebar.isOpen('{{ $section }}') ? 'rotate-90 text-info' : ''"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            viewBox="0 0 24 24"
        >
            <path d="M9 5l7 7-7 7"/>
        </svg>
    </button>
    <div
        x-show="$store.sidebar.isOpen('{{ $section }}')"
        x-collapse.duration.200ms
        class="px-2 py-3 space-y-1 bg-base-100"
    >
        {{ $slot }}
    </div>
</div>
