<div class="bg-base-200 p-4 rounded mt-4">
    <div class="text-lg font-medium mb-4">Tribute
        <span class="badge badge-sm badge-soft badge-accent">{{ $character->tribute->count() }}</span>
    </div>
    @foreach ($character->tribute as $tribute)
        <div class="flex items-center gap-3 mt-2 p-2 rounded odd:bg-base-100 even:bg-base-200/50">
            <div class="text-sm font-bold">
                {{ $tribute->_tribute?->name ?? 'Unknown' }}
            </div>
            <div class="flex-1 border-b border-dotted border-base-content/20"></div>
            <div class="font-medium text-xs font-mono">
                {{ (int) ($tribute->tier ?? 0) + 1 }}
            </div>
        </div>
    @endforeach
</div>
