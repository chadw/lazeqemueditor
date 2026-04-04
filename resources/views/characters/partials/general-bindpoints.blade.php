<div class="bg-base-200 p-4 rounded mt-4">
    <div class="text-lg font-medium mb-4">Bind Points
        <span class="badge badge-sm badge-soft badge-accent">{{ $character->bindpoint->count() }}</span>
    </div>
    @foreach ($character->bindpoint as $bp)
        <div class="flex items-center gap-3 mt-2 p-2 rounded odd:bg-base-100 even:bg-base-200/50">
            <div class="text-sm font-bold w-24">
                {{ config('everquest.bind_points')[$bp->slot] ?? 'Bind '.$bp->slot }}
            </div>
            <div class="text-sm text-muted">{{ optional($bp->zone)->short_name ?? $bp->zone_id }}</div>
            <div class="flex-1 border-b border-dotted border-base-content/20"></div>
            <div class="font-medium text-xs font-mono">
                X: {{ $bp->x }}, Y: {{ $bp->y }}, Z: {{ $bp->z }}, Heading: {{ $bp->heading }}
            </div>
        </div>
    @endforeach
</div>
