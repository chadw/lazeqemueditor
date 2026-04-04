{{-- Level Loss --}}
@php
    $d = $log->event_data;
@endphp
<div class="flex flex-col gap-1">
    <div class="text-sm">
        From level
        <span class="font-medium">
            {{ $d['from_level'] }}
        </span>
        to
        <span class="font-medium">
            {{ $d['to_level'] }}
        </span>
        <span class="badge badge-soft badge-error ms-2">
            {{ $d['levels_lost'] ?? 1 }} level(s) lost
        </span>
    </div>
</div>
