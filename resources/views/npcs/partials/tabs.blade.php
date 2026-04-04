<div class="tabs tabs-lift" data-tab-group="npc_tabs">
    <input type="radio" name="n_tabs" value="main" class="tab" aria-label="Main"
        @if(! request()->has('tab') || request()->get('tab') === 'main') checked @endif />
    <div class="tab-content bg-base-100 border-base-300 p-6" x-data="formTracker">
        <form method="POST" action="{{ route('npcs.update', $npc) }}" id="npc-edit-form">
            @csrf
            @method('PUT')

            @include('npcs.forms.form', ['npc' => $npc])

            <div class="mt-6 flex justify-end gap-2">
                <button type="submit" class="btn btn-soft btn-success">
                    Save NPC
                </button>
            </div>
        </form>
    </div>
    <label class="tab">
        <input type="radio" name="n_tabs" value="spawns" @if(request()->get('tab') === 'spawns') checked @endif />
        Spawns
        <div class="badge badge-xs badge-soft badge-info ml-2">{{ $npc?->spawnEntries->count() }}</div>
    </label>
    <div class="tab-content bg-base-100 border-base-300 p-6">
        @if ($npc?->spawnEntries->isNotEmpty())
            @include('npcs.tabs.spawns', [
                'npc' => $npc,
            ])
        @else
            <x-ui.alert-info>
                No Spawns for this npc.
            </x-ui.alert-info>
        @endif
    </div>
    <label class="tab">
        <input type="radio" name="n_tabs" value="loot" @if(request()->get('tab') === 'loot') checked @endif />
        Loot
        <div class="badge badge-xs badge-soft badge-info ml-2">
            {{ $npc?->lootTable?->loottableEntries?->count() ?? 0 }}
        </div>
    </label>
    <div class="tab-content bg-base-100 border-base-300 p-6">
        @include('npcs.tabs.loottables', [
            'npc' => $npc,
        ])
    </div>
    <label class="tab">
        <input type="radio" name="n_tabs" value="spells" @if(request()->get('tab') === 'spells') checked @endif />
        NPC Spells
        <div class="badge badge-xs badge-soft badge-info ml-2">{{ $npc?->npcSpellset?->npcSpellEntries->count() }}</div>
    </label>
    <div class="tab-content bg-base-100 border-base-300 p-6">
        @include('npcs.tabs.spells', [
            'npc' => $npc,
        ])
    </div>
    <label class="tab">
        <input type="radio" name="n_tabs" value="faction" @if(request()->get('tab') === 'faction') checked @endif />
        Faction
    </label>
    <div class="tab-content bg-base-100 border-base-300 p-6">
        @include('npcs.tabs.faction', [
            'npc' => $npc,
        ])
    </div>
</div>
