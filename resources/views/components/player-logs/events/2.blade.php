@php
    $d = $log->event_data;
@endphp
<div class="flex flex-col gap-1">
    <div class="text-sm">
        From
        <span class="font-medium">
            {{ $d['from_zone_short_name'] }}
            @if ($d['from_instance_id'] !== 0)
                <span class="badge badge-sm badge-soft badge-secondary">
                    (dz)
                </span>
            @endif
        </span>
        to
        <span class="font-medium">
            {{ $d['to_zone_short_name'] }}
            @if ($d['to_instance_id'] !== 0)
                <span class="badge badge-sm badge-soft badge-secondary">
                    (dz)
                </span>
            @endif
        </span>
    </div>
</div>
