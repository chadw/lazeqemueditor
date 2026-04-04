{{-- Level Gain --}}
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
        <span class="badge badge-soft badge-success ms-2">
            {{ $d['levels_gained'] ?? 1 }} level(s) gained
        </span>
    </div>
</div>
