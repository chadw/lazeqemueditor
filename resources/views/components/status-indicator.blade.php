@if ($ok)
    {{ $slot }}
@else
    <div aria-label="error" class="status status-sm status-error"></div>
@endif
