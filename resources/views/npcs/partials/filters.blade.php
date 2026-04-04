<form method="GET" action="{{ route('npcs.index') }}" class="flex gap-2 items-end"
    x-data="npcSelector({
        zone: @js(request()->get('zone')),
        version: @js(request()->get('v')),
        npc: @js(request()->route('npc'))
    })"
    x-init="versions = @js($versions ?? []);
    init()"
    @submit.prevent="(function(e){ const q = (e.target.q && e.target.q.value || '').trim(); if (q && /^\d+$/.test(q)) { window.location = '/npcs/' + q + '/edit'; return; } if (q) { if (e.target.zone) e.target.zone.value = ''; if (e.target.version) e.target.version.value = ''; } e.target.submit(); })(event)">
    <div class="w-80">
        <label class="label label-text">NPC ID or Name</label>
        <div class="flex gap-2">
            <input name="q" value="{{ $filters['q'] ?? request('q') }}" type="text"
                class="input flex-1" placeholder="id or name" />
            <button class="btn btn-soft btn-primary" type="submit">Search</button>
        </div>
    </div>

    <div class="w-60">
        <label class="label label-text">Zone</label>
        <select id="filter-zone" name="zone" x-model="filters.zone" @change="zoneChanged()"
            class="select w-full">
            <option value="">Any</option>
            @foreach ($zones as $z)
                <option value="{{ $z->zoneidnumber }}" @selected((string) ($filters['zone'] ?? request('zone')) === (string) $z->zoneidnumber)>{{ $z->short_name }}
                    ({{ $z->zoneidnumber }})</option>
            @endforeach
        </select>
    </div>

    <div class="w-20">
        <label class="label">Version</label>
        <select id="filter-version" name="version" x-model.number="filters.version" @change="search()"
            class="select w-full">
            <option value="0">v0</option>
            <template x-for="v in versions" :key="v.version">
                <option :value="v.version" x-text="`v${v.version}`"
                    :selected="Number(filters.version) === Number(v.version)"></option>
            </template>
        </select>
    </div>
    <div class="w-64" x-show="filters.zone">
        <label class="label label-text">NPCs in Zone</label>
        <div class="relative">
            <select x-ref="npcSelect" x-model.number="selectedNpc" :disabled="loading"
                @change="if(selectedNpc) window.location = '/npcs/' + selectedNpc + '/edit?zone=' + filters.zone + '&v=' + filters.version"
                class="select w-full">
                <option value="">— Select NPC —</option>
                <template x-for="r in results" :key="r.id">
                    <option :value="Number(r.id)" x-text="`${r.id} - ${r.name}`"></option>
                </template>
            </select>
            <div x-show="loading" class="absolute inset-y-0 right-2 flex items-center pr-2">
                <svg class="animate-spin h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
            </div>
        </div>
    </div>
</form>
