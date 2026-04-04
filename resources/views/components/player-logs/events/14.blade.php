<div class="flex flex-col gap-1">
    <div class="flex items-center gap-2">
        <x-item-link
            :item_id="$log->event_data['item_id']['item']['id']"
            :item_name="$log->event_data['item_id']['item']['Name']"
            :item_icon="$log->event_data['item_id']['item']['icon']"
            item_class="flex-inline"
        />
        <span class="text-sm font-medium text-base-content/80">
            <span class="badge badge-sm badge-soft badge-accent">
                x{{ $log->event_data['charges'] ?? 1 }}
            </span>
            - {{ $log->event_data['corpse_name'] }}
        </span>
    </div>
</div>
