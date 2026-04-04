<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'LazEQEmu Editor')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css'])
</head>
<body x-data x-cloak>
    <div class="drawer h-screen" x-bind:class="$store.sidebar && !$store.sidebar.collapsed ? 'lg:drawer-open' : ''">
        <input id="mobile-drawer" type="checkbox" class="drawer-toggle" />
        <div class="drawer-content flex flex-col overflow-hidden">
            @include('layouts.partials.topbar')
            <main class="flex-1 p-6 bg-base-300 flex flex-col overflow-auto min-h-0">
                @yield('content')
            </main>
            @include('layouts.partials.footer')
        </div>
        <div class="drawer-side">
            <label for="mobile-drawer" class="drawer-overlay"></label>
            @include('layouts.partials.sidebar')
        </div>
    </div>
    @vite(['resources/js/app.js'])
    <div x-data x-show="$store.tooltip.visible" x-html="$store.tooltip.content" x-ref="tooltip"
        x-transition x-cloak id="global-tooltip"
        class="fixed z-50 bg-base-200 rounded shadow-[0px_0px_15px_0px_rgba(0,0,0,0.7)] max-w-lg text-sm pointer-events-none"
        style="position: absolute; display: none; top: 0; left: 0"></div>
    <x-ui.toast />
    @stack('scripts')
</body>
</html>
