@php
    $classes = collect(explode('|', $globalLoot->class))
        ->map(fn($id) => config('everquest.classes_abbr')[$id] ?? null)
        ->filter()
        ->values()
        ->all();

    $races = collect(explode('|', $globalLoot->race))
        ->map(fn($id) => config('everquest.db_races')[$id] ?? null)
        ->filter()
        ->values()
        ->all();

    $bodytypes = collect(explode('|', $globalLoot->bodytype))
        ->map(fn($id) => config('everquest.db_bodytypes')[$id] ?? null)
        ->filter()
        ->values()
        ->all();

    $zonesList = collect(explode('|', $globalLoot->zone))
        ->map(fn($id) => $zones[$id] ?? null)
        ->filter()
        ->values()
        ->all();

    $globalLoot->_classes = explode('|', $globalLoot->class);
    $globalLoot->_races = explode('|', $globalLoot->race);
    $globalLoot->_bodytypes = explode('|', $globalLoot->bodytype);
    $globalLoot->_zones = explode('|', $globalLoot->zone);
@endphp
<div class="card bg-base-100 shadow mb-6" x-data data-gloot='@json($globalLoot)'>
    <div class="card-header flex items-center justify-between px-6 py-4 border-b border-b-base-content/10">
        <h2 class="text-lg font-semibold">
            Global Loot - {{ $globalLoot->description ?? 'Unnamed' }}
        </h2>

        <button type="button" class="join-item btn btn-sm btn-soft"
            @click="$store.modalForm.openEdit(
                $el.closest('[data-gloot]').dataset.gloot,
                '{{ route('global-loot.update', $globalLoot) }}',
                {
                    modal: 'global-loot',
                    resourceName: 'Edit Global Loot'
                }
            )">
            <x-ui.icon name="edit" /> Edit
        </button>
    </div>

    <div class="card-body space-y-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <x-ui.status :status="$globalLoot->enabled ? 'success' : 'error'" :label="$globalLoot->enabled ? 'Enabled' : 'Disabled'" />
            <x-ui.status :status="match ($globalLoot->hot_zone) {
                1 => 'success',
                0 => 'error',
                default => 'neutral',
            }" :label="match ($globalLoot->hot_zone) {
                1 => 'Must Be Hot Zone',
                0 => 'Must Not Be Hot Zone',
                default => 'Hot Zone Not Used',
            }" />
            <x-ui.status :status="match ($globalLoot->rare) {
                1 => 'warning',
                0 => 'error',
                default => 'neutral',
            }" :label="match ($globalLoot->rare) {
                1 => 'Rare Only',
                0 => 'Not Rare',
                default => 'Rare Not Used',
            }" />
            <x-ui.status :status="match ($globalLoot->raid) {
                1 => 'warning',
                0 => 'error',
                default => 'neutral',
            }" :label="match ($globalLoot->raid) {
                1 => 'Raid Only',
                0 => 'Non-Raid',
                default => 'Raid Not Used',
            }" />
        </div>
        <div class="grid grid-cols-4 gap-6">
            <div>
                <h3 class="font-semibold mb-2">Race</h3>
                @php
                    $race = limitedList($races);
                @endphp
                @if (empty($race['all']))
                    <span class="text-base-content/60">All</span>
                @else
                    <div class="flex flex-wrap gap-1">
                        {{ implode(', ', $race['shown']) }}

                        @if ($race['remaining'] > 0)
                            <span class="text-primary cursor-help tooltip" data-tip="{{ implode(', ', $race['all']) }}">
                                +{{ $race['remaining'] }} more
                            </span>
                        @endif
                    </div>
                @endif
            </div>
            <div>
                <h3 class="font-semibold mb-2">Class</h3>
                @php
                    $class = limitedList($classes);
                @endphp
                @if (empty($class['all']))
                    <span class="text-base-content/60">All</span>
                @else
                    <div class="flex flex-wrap gap-1">
                        {{ implode(', ', $class['shown']) }}

                        @if ($class['remaining'] > 0)
                            <span class="text-primary cursor-help tooltip"
                                data-tip="{{ implode(', ', $class['all']) }}">
                                +{{ $class['remaining'] }} more
                            </span>
                        @endif
                    </div>
                @endif
            </div>
            <div>
                <h3 class="font-semibold mb-2">Body Type</h3>
                @php
                    $bodytype = limitedList($bodytypes);
                @endphp
                @if (empty($bodytype['all']))
                    <span class="text-base-content/60">All</span>
                @else
                    <div class="flex flex-wrap gap-1">
                        {{ implode(', ', $bodytype['shown']) }}

                        @if ($bodytype['remaining'] > 0)
                            <span class="text-primary cursor-help tooltip"
                                data-tip="{{ implode(', ', $bodytype['all']) }}">
                                +{{ $bodytype['remaining'] }} more
                            </span>
                        @endif
                    </div>
                @endif
            </div>
            <div>
                <h3 class="font-semibold mb-2">Zone</h3>
                @php
                    $zone = limitedList($zonesList, 3);
                @endphp
                @if (empty($zone['all']))
                    <span class="text-base-content/60">All</span>
                @else
                    <div class="flex flex-wrap gap-1">
                        {{ implode(', ', $zone['shown']) }}

                        @if ($zone['remaining'] > 0)
                            <span class="text-primary cursor-help tooltip"
                                data-tip="{{ implode(', ', $zone['all']) }}">
                                +{{ $zone['remaining'] }} more
                            </span>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
