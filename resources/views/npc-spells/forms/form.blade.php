<div class="card bg-base-200 card-sm shadow-sm mb-6">
    <div class="card-body">
        <h2 class="card-title">NPC Spell</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <x-form.input
                name="name"
                label="Name"
                tooltip=""
                :value="$npcSpell->name"
                required
            />
            <x-form.select
                name="parent_list"
                label="Parent"
                tooltip=""
                :options="[0 => 'None'] + $allNpcSpells"
                :selected="$npcSpell->parent_list"
                keyInOption="true"
            />
            <x-form.input
                name="fail_recast"
                label="Fail Recast"
                tooltip=""
                type="number"
                min="0"
                :value="$npcSpell->fail_recast"
            />
        </div>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
    <div class="card bg-base-200 card-sm shadow-sm mb-6">
        <div class="card-body">
            <h2 class="card-title">Attack Proc</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @php
                    $prefillAttack = $npcSpell?->attackProcSpell ? [
                        'id' => $npcSpell->attackProcSpell->id,
                        'name' => $npcSpell->attackProcSpell->name,
                    ] : null;
                @endphp
                <div
                    x-data='ajaxSelect({
                        searchUrl: "/spells/search",
                        prefillValue: @json($prefillAttack),
                        allowNone: true,
                        noneId: -1,
                        keyInOption: true,
                    })'
                    x-init="init()"
                >
                    <label class="label">Spell ID</label>
                    <select
                        x-ref="select"
                        name="attack_proc"
                        class="w-full"
                    ></select>
                </div>
                <x-form.input
                    name="proc_chance"
                    label="Proc Chance"
                    tooltip="Proc Chance: 0 = Never, 100 = Always"
                    type="number"
                    min="0"
                    max="100"
                    :value="$npcSpell->proc_chance"
                />
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm mb-6">
        <div class="card-body">
            <h2 class="card-title">Range Proc</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @php
                    $prefillRange = $npcSpell?->rangeProcSpell ? [
                        'id' => $npcSpell->rangeProcSpell->id,
                        'name' => $npcSpell->rangeProcSpell->name,
                    ] : null;
                @endphp
                <div
                    x-data='ajaxSelect({
                        searchUrl: "/spells/search",
                        prefillValue: @json($prefillRange),
                        allowNone: true,
                        noneId: -1,
                        keyInOption: true,
                    })'
                    x-init="init()"
                >
                    <label class="label">Spell ID</label>
                    <select
                        x-ref="select"
                        name="range_proc"
                        class="w-full"
                    ></select>
                </div>
                <x-form.input
                    name="rproc_chance"
                    label="Proc Chance"
                    tooltip="Ranged Proc Chance: 0 = Never, 100 = Always"
                    type="number"
                    min="0"
                    max="100"
                    :value="$npcSpell->rproc_chance"
                />
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm mb-6">
        <div class="card-body">
            <h2 class="card-title">Defensive Proc</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @php
                    $prefillDefensive = $npcSpell?->defensiveProcSpell ? [
                        'id' => $npcSpell->defensiveProcSpell->id,
                        'name' => $npcSpell->defensiveProcSpell->name,
                    ] : null;
                @endphp
                <div
                    x-data='ajaxSelect({
                        searchUrl: "/spells/search",
                        prefillValue: @json($prefillDefensive),
                        allowNone: true,
                        noneId: -1,
                        keyInOption: true,
                    })'
                    x-init="init()"
                >
                    <label class="label">Spell ID</label>
                    <select
                        x-ref="select"
                        name="defensive_proc"
                        class="w-full"
                    ></select>
                </div>
                <x-form.input
                    name="dproc_chance"
                    label="Proc Chance"
                    tooltip="Defensive Proc Chance: 0 = Never, 100 = Always"
                    type="number"
                    min="0"
                    max="100"
                    :value="$npcSpell->dproc_chance"
                />
            </div>
        </div>
    </div>
</div>
{{-- Unused https://docs.eqemu.dev/schema/npcs/npc_spells/ --}}
{{--
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
    <div class="card bg-base-200 card-sm shadow-sm mb-6">
        <div class="card-body">
            <h2 class="card-title">Engaged</h2>
            <div class="grid grid-cols-1 sm:grid-cols-5 gap-4">
                <x-form.input
                    name="engaged_b_self_chance"
                    label="B Self Chance"
                    tooltip=""
                    :value="$npcSpell->engaged_b_self_chance"
                />
                <x-form.input
                    name="engaged_b_other_chance"
                    label="B Other Chance"
                    tooltip=""
                    :value="$npcSpell->engaged_b_other_chance"
                />
                <x-form.input
                    name="engaged_d_chance"
                    label="D Chance"
                    tooltip=""
                    :value="$npcSpell->engaged_d_chance"
                />
                <x-form.input
                    name="engaged_no_sp_recast_min"
                    label="No Sp Recast Min"
                    tooltip=""
                    :value="$npcSpell->engaged_no_sp_recast_min"
                />
                <x-form.input
                    name="engaged_no_sp_recast_max"
                    label="No Sp Recast Max"
                    tooltip=""
                    :value="$npcSpell->engaged_no_sp_recast_max"
                />
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm mb-6">
        <div class="card-body">
            <h2 class="card-title">Pursue</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <x-form.input
                    name="pursue_no_sp_recast_min"
                    label="No Sp Recast Min"
                    tooltip=""
                    :value="$npcSpell->pursue_no_sp_recast_min"
                />
                <x-form.input
                    name="pursue_no_sp_recast_max"
                    label="No Sp Recast Max"
                    tooltip=""
                    :value="$npcSpell->pursue_no_sp_recast_max"
                />
                <x-form.input
                    name="pursue_d_chance"
                    label="D Chance"
                    tooltip=""
                    :value="$npcSpell->pursue_d_chance"
                />
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm mb-6">
        <div class="card-body">
            <h2 class="card-title">Idle</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <x-form.input
                    name="idle_no_sp_recast_min"
                    label="No Sp Recast Min"
                    tooltip=""
                    :value="$npcSpell->idle_no_sp_recast_min"
                />
                <x-form.input
                    name="idle_no_sp_recast_max"
                    label="No Sp Recast Max"
                    tooltip=""
                    :value="$npcSpell->idle_no_sp_recast_max"
                />
                <x-form.input
                    name="idle_b_chance"
                    label="B Chance"
                    tooltip=""
                    :value="$npcSpell->idle_b_chance"
                />
            </div>
        </div>
    </div>
</div>
--}}
<div class="gap-4 text-right">
    <button type="submit" class="btn btn-sm btn-soft btn-success">Save NPC Spell</button>
</div>
