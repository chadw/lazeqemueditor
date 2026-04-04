<div>
    @if ($character->corpses && $character->corpses->count())
        <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($character->corpses as $corpse)
                <article class="bg-base-100 rounded-lg shadow-sm overflow-hidden border border-base-content/10">
                    <div class="p-4 border-b border-base-content/10 flex items-start justify-between gap-4 bg-neutral/40">
                        <div class="min-w-0">
                            <div class="text-sm font-medium truncate">
                                {{ optional($corpse->zone)->short_name ?? ($corpse->zone ?? 'Unknown') }}</div>
                            <div class="text-xs text-muted mt-1">Zone ID: {{ $corpse->zone_id ?? '—' }}</div>
                        </div>
                        <div class="text-xs text-right text-muted">
                            <div>Death:
                                {{ $corpse->time_of_death ? \Carbon\Carbon::parse($corpse->time_of_death)->format('Y-m-d H:i') : '—' }}
                            </div>
                            <div class="text-xs">
                                {{ $corpse->time_of_death ? \Carbon\Carbon::parse($corpse->time_of_death)->diffForHumans() : '' }}
                            </div>
                        </div>
                    </div>

                    <div class="p-4 space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="text-sm text-muted w-24">Coords</div>
                            <div class="flex-1 font-mono text-sm">X: {{ $corpse->x ?? '—' }}, Y:
                                {{ $corpse->y ?? '—' }}, Z: {{ $corpse->z ?? '—' }}, H:
                                {{ $corpse->heading ?? ($corpse->h ?? '—') }}</div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="text-sm text-muted w-24">Currency</div>
                            <div class="flex-1 text-sm">
                                <x-currency
                                    :platinum="$corpse->platinum"
                                    :gold="$corpse->gold"
                                    :silver="$corpse->silver"
                                    :copper="$corpse->copper"
                                />
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="text-sm text-muted w-24">Items</div>
                            <div class="flex-1 text-sm font-medium">{{ optional($corpse->corpseItems)->count() ?? 0 }}
                                items on corpse</div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="text-sm text-muted w-24">Level</div>
                            <div class="flex-1 text-sm font-medium">{{ $corpse->level ?? '—' }}</div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="text-sm text-muted w-24">EXP</div>
                            <div class="flex-1 text-sm font-medium">{{ number_format($corpse->exp ?? 0) }}</div>
                        </div>

                        <div class="flex items-center gap-2 flex-wrap">
                            @if ($corpse->is_rezzed)
                                <span class="badge badge-sm badge-soft badge-success">Rezzed</span>
                            @else
                                <span class="badge badge-sm badge-soft">Not Rezzed</span>
                            @endif

                            @if ($corpse->is_buried)
                                <span class="badge badge-sm badge-soft badge-warning">Buried</span>
                            @else
                                <span class="badge badge-sm badge-soft">Not Buried</span>
                            @endif

                            @if ($corpse->is_locked)
                                <span class="badge badge-sm badge-soft badge-error">Locked</span>
                            @else
                                <span class="badge badge-sm badge-soft">Unlocked</span>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <p class="text-muted mt-2">No corpses on record.</p>
    @endif
</div>
