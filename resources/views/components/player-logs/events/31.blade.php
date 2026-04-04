<div class="flex flex-col gap-1">
    <div class="flex items-center gap-2">
        <div class="text-gray-400">
            Killed by
            <span class="text-gray-200 font-medium">
                {{ $log->event_data['killer_name'] }}
            </span>
        </div>
    </div>
</div>
