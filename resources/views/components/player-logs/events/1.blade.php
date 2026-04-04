<div class="flex flex-col gap-1">
    <div class="flex items-center gap-2">
        <div class="text-gray-400">
            Used
            <span class="text-gray-200 font-medium">
                {{ $log->event_data['message'] }}
            </span>
            @if ($log->event_data['target'] && $log->event_data['target'] !== 'NONE')
                on
                <span class="text-gray-200 font-medium">
                    {{ $log->event_data['target'] }}
                </span>
            @endif
        </div>
    </div>
</div>
