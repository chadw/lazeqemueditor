@props([
    'label' => '',
    'ok' => '',
])

@if ($ok)
    <div aria-label="status" class="status status-lg status-success animate-pulse"></div> {{ $label }}
@else
    <div aria-label="error" class="status status-sm status-error"></div> {{ $label }}
@endif
