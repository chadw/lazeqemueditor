<div class="tabs tabs-lift" data-tab-group="zone_tabs">
    <input type="radio" name="z_tabs" class="tab" value="main" aria-label="Main" checked="checked"
        @if(! request()->has('tab') || request()->get('tab') === 'main') checked @endif
    />
    <div class="tab-content bg-base-100 border-base-300 p-6">
        <form method="POST" action="{{ route('zones.update', $zone) }}">
            @csrf
            @method('PUT')

            @include('zones.forms.form', ['zone' => $zone])

            <div class="mt-6 flex justify-end gap-2">
                <button type="submit" class="btn btn-soft btn-success">
                    Save Zone
                </button>
            </div>
        </form>
    </div>
    <label class="tab">
        <input type="radio" name="z_tabs" value="zonepoints"
            @if(request()->get('tab') === 'zonepoints') checked @endif
        />
        Zone Points
        <div class="badge badge-xs badge-soft badge-info ml-2">{{ $zone?->zonepoints->count() }}</div>
    </label>
    <div class="tab-content bg-base-100 border-base-300 p-6">
        @include('zones.tabs.zone-points', ['zone' => $zone])
    </div>
    <label class="tab">
        <input type="radio" name="z_tabs" value="groundspawns"
            @if(request()->get('tab') === 'groundspawns') checked @endif
        />
        Ground Spawns
        <div class="badge badge-xs badge-soft badge-info ml-2">{{ $zone?->groundspawns->count() }}</div>
    </label>
    <div class="tab-content bg-base-100 border-base-300 p-6">
        @include('zones.tabs.ground-spawns', ['zone' => $zone])
    </div>
    <label class="tab">
        <input type="radio" name="z_tabs" value="fishing"
            @if(request()->get('tab') === 'fishing') checked @endif
        />
        Fishing
        <div class="badge badge-xs badge-soft badge-info ml-2">{{ $fishing->count() }}</div>
    </label>
    <div class="tab-content bg-base-100 border-base-300 p-6">
        @include('zones.tabs.fishing', ['zone' => $zone, 'fishing' => $fishing])
    </div>
    <label class="tab">
        <input type="radio" name="z_tabs" value="forages"
            @if(request()->get('tab') === 'forages') checked @endif
        />
        Forage
        <div class="badge badge-xs badge-soft badge-info ml-2">{{ $forages->count() }}</div>
    </label>
    <div class="tab-content bg-base-100 border-base-300 p-6">
        @include('zones.tabs.forage', ['zone' => $zone, 'forages' => $forages])
    </div>
    <label class="tab">
        <input type="radio" name="z_tabs" value="blockedspells"
            @if(request()->get('tab') === 'blockedspells') checked @endif
        />
        Blocked Spells
        <div class="badge badge-xs badge-soft badge-info ml-2">{{ $zone?->blockedSpells->count() }}</div>
    </label>
    <div class="tab-content bg-base-100 border-base-300 p-6">
        @include('zones.tabs.blocked-spells', ['zone' => $zone])
    </div>
    <label class="tab">
        <input type="radio" name="z_tabs" value="doors"
            @if(request()->get('tab') === 'doors') checked @endif
        />
        Doors
        <div class="badge badge-xs badge-soft badge-info ml-2">{{ $zone?->doors->count() }}</div>
    </label>
    <div class="tab-content bg-base-100 border-base-300 p-6">
            @include('zones.tabs.doors', ['zone' => $zone])
    </div>
    <label class="tab">
        <input type="radio" name="z_tabs" value="traps"
            @if(request()->get('tab') === 'traps') checked @endif
        />
        Traps
        <div class="badge badge-xs badge-soft badge-info ml-2">{{ $zone?->traps->count() }}</div>
    </label>
    <div class="tab-content bg-base-100 border-base-300 p-6">
        @include('zones.tabs.traps', ['zone' => $zone])
    </div>
    <label class="tab">
        <input type="radio" name="z_tabs" value="objects"
            @if(request()->get('tab') === 'objects') checked @endif
        />
        Objects
        <div class="badge badge-xs badge-soft badge-info ml-2">{{ $zone?->objects->count() }}</div>
    </label>
    <div class="tab-content bg-base-100 border-base-300 p-6">
        @include('zones.tabs.objects', ['zone' => $zone])
    </div>
</div>
