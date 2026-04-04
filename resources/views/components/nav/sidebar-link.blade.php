@props([
    'route' => null,
    'active' => null,
])

@php
    $activeRoute = $active ?? $route;

    if (is_array($activeRoute)) {
        $isActive = collect($activeRoute)
            ->contains(fn ($pattern) => request()->routeIs($pattern));
    } else {
        $isActive = $activeRoute
            ? request()->routeIs($activeRoute)
            : false;
    }
    $hasChildren = trim($slot) !== '';
@endphp

@if ($hasChildren && $route === null)
    <div x-data="{ open: {{ $isActive ? 'true' : 'false' }} }" class="space-y-1">
        <button
            type="button"
            @click="open = !open"
            class="flex items-center justify-between w-full px-3 py-2 rounded-md transition
                {{ $isActive ? 'bg-neutral/20 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}"
        >
            <span>{{ $attributes->get('label') }}</span>
            <svg
                class="w-4 h-4 transition-transform duration-200"
                x-bind:class="open ? 'rotate-90' : ''"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                viewBox="0 0 24 24"
            >
                <path d="M9 5l7 7-7 7"/>
            </svg>
        </button>

        <div x-show="open"
             x-collapse
             class="ml-3 space-y-1">
            {{ $slot }}
        </div>
    </div>
@else
    <a href="{{ route($route) }}"
       class="flex items-center px-3 py-2 rounded-md transition
          {{ $isActive ? 'bg-neutral/20 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
        <span>{{ $slot }}</span>
    </a>
@endif
