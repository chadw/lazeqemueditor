@props([
    'status' => 'neutral',
    'label' => null,
])

<div class="flex items-center gap-2">
    <div class="status status-{{ $status }}"></div>
    @if ($label)
        <span class="text-sm">{{ $label }}</span>
    @endif
</div>
