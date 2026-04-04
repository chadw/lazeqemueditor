@props([
    'type' => 'checkbox', // checkbox | input | select
])

@if (in_array($type, ['input', 'select']))
    <div class="relative">
        <div class="absolute right-10 top-0 -translate-y-8 flex items-center gap-1 pointer-events-none">
            <span x-cloak x-show="saving" class="loading loading-xs"></span>
            <span x-cloak x-show="saved" class="text-success text-sm">✓</span>
            <span x-cloak x-show="error" class="text-error text-sm">✕</span>
        </div>
    </div>
@else
    <div class="relative">
        <div class="absolute left-10 top-0 -translate-y-5 flex items-center gap-1 pointer-events-none">
            <span x-cloak x-show="saving" class="loading loading-xs"></span>
            <span x-cloak x-show="saved" class="text-success text-sm">✓</span>
            <span x-cloak x-show="error" class="text-error text-sm">✕</span>
        </div>
    </div>
@endif
