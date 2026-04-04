@props([
    'type' => 'info',
])

<div role="alert" {{ $attributes->merge(['class' => "alert alert-{$type} alert-soft"]) }}>
    <span>
        {{ $slot }}
    </span>
</div>
