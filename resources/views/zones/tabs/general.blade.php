<div class="space-y-6" x-data="formTracker">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <span class="badge badge-neutral badge-lg">{{ $zone->zoneidnumber }}</span>
                    <div>
                        <div class="text-lg font-semibold">{{ $zone->long_name }}</div>
                        <div class="text-sm text-base-content/60">{{ $zone->short_name }} • v{{ $zone->version }}</div>
                    </div>
                </div>
                <div class="text-sm text-right">
                    <div>Expansion: <span class="font-medium">{{ config('everquest.expansions')[$zone->expansion] ?? 'Other - ' . $zone->expansion }}</span></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h2 class="card-title">General</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2">
                <div class="md:col-span-2">
                    <x-form.input
                        name="long_name"
                        label="Name"
                        :value="$zone->long_name"
                        required
                    />
                    <x-form.textarea
                        name="note"
                        label="Note"
                        rows="2"
                        :value="$zone->note"
                        wrapper-class="mt-4"
                    />
                </div>

                <div class="space-y-3">
                    <x-form.select
                        name="expansion"
                        label="Expansion"
                        :options="config('everquest.expansions')"
                        keyInOption="true"
                        :selected="$zone->expansion"
                    />
                    <x-form.input
                        name="version"
                        label="Version"
                        type="number"
                        min="0"
                        :value="$zone->version"
                    />
                    <x-form.input
                        name="zone_exp_multiplier"
                        label="Exp Multiplier"
                        type="number"
                        step="any"
                        :value="$zone->zone_exp_multiplier"
                    />
                    <x-form.input
                        name="shutdowndelay"
                        label="Shutdown"
                        type="number"
                        :value="$zone->shutdowndelay"
                    />
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Location & Movement</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-2">
                <x-form.input
                    name="safe_x"
                    label="Safe X"
                    type="number"
                    step="any"
                    :value="$zone->safe_x"
                />
                <x-form.input
                    name="safe_y"
                    label="Safe Y"
                    type="number"
                    step="any"
                    :value="$zone->safe_y"
                />
                <x-form.input
                    name="safe_z"
                    label="Safe Z"
                    type="number"
                    step="any"
                    :value="$zone->safe_z"
                />
                <x-form.input
                    name="safe_heading"
                    label="Safe Heading"
                    type="number"
                    step="any"
                    :value="$zone->safe_heading"
                />
                <x-form.input
                    name="walkspeed"
                    label="Walkspeed"
                    type="number"
                    step="any"
                    :value="$zone->walkspeed"
                />
                <x-form.input
                    name="gravity"
                    label="Gravity"
                    type="number"
                    step="any"
                    :value="$zone->gravity"
                />
                <x-form.input
                    name="client_update_range"
                    label="Client Update Range"
                    type="number"
                    :value="$zone->client_update_range"
                />
                <x-form.input
                    name="insttype"
                    label="Inst Type"
                    type="number"
                    :value="$zone->insttype"
                />
            </div>
        </div>
    </div>

    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Systems & Thresholds</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-6 gap-4 mt-2">
                <x-form.select
                    name="type"
                    label="Type"
                    :options="[
                        0 => 'Unknown',
                        1 => 'Regular',
                        2 => 'Instanced',
                        3 => 'Hybrid',
                        4 => 'Raid',
                        5 => 'City',
                    ]"
                    :selected="$zone->type"
                />
                <x-form.input
                    name="npc_max_aggro_dist"
                    label="NPC Max Aggro"
                    type="number"
                    :value="$zone->npc_max_aggro_dist"
                />
                <x-form.select
                    name="graveyard_id"
                    label="Graveyard"
                    :options="[0 => 'Unknown'] + $graveyards"
                    :selected="$zone->graveyard_id"
                />
                <x-form.input
                    name="timezone"
                    label="Timezone"
                    type="number"
                    :value="$zone->timezone"
                />
                <x-form.input
                    name="time_type"
                    label="Time Type"
                    type="number"
                    :value="$zone->time_type"
                />
                <x-form.input
                    name="ztype"
                    label="Zone Type"
                    type="number"
                    :value="$zone->ztype"
                />
                <x-form.input
                    name="ruleset"
                    label="Ruleset"
                    type="number"
                    :value="$zone->ruleset"
                />
                <x-form.input
                    name="underworld"
                    label="Underworld"
                    type="number"
                    step="any"
                    :value="$zone->underworld"
                />
                <x-form.input
                    name="underworld_teleport_index"
                    label="Underworld Teleport Index"
                    type="number"
                    :value="$zone->underworld_teleport_index"
                />
                <x-form.input
                    name="seconds_before_idle"
                    label="Seconds Before Idle"
                    type="number"
                    :value="$zone->seconds_before_idle"
                />
                <x-form.select
                    name="canbind"
                    label="Can Bind"
                    :options="[
                        0 => 'No',
                        1 => 'Self',
                        2 => 'Others',
                    ]"
                    :selected="$zone->canbind"
                />
                <x-form.input
                    name="map_file_name"
                    label="Map File"
                    :value="$zone->map_file_name"
                />
            </div>
        </div>
    </div>

    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Damage & Regen</h2>
            <div class="grid sm:grid-cols-3 md:grid-cols-6 gap-4 mt-2">
                <x-form.input
                    name="lava_damage"
                    label="Lava Damage"
                    type="number"
                    :value="$zone->lava_damage"
                />
                <x-form.input
                    name="min_lava_damage"
                    label="Min Lava Damage"
                    type="number"
                    :value="$zone->min_lava_damage"
                />
                <x-form.input
                    name="shard_at_player_count"
                    label="Shard at Player Count"
                    type="number"
                    :value="$zone->shard_at_player_count"
                />
                <x-form.input
                    name="fast_regen_hp"
                    label="Regen HP"
                    type="text"
                    type="number"
                    :value="$zone->fast_regen_hp"
                />
                <x-form.input
                    name="fast_regen_mana"
                    label="Regen Mana"
                    type="text"
                    type="number"
                    :value="$zone->fast_regen_mana"
                />
                <x-form.input
                    name="fast_regen_endurance"
                    label="Regen End"
                    type="number"
                    :value="$zone->fast_regen_endurance"
                />
            </div>
        </div>
    </div>

    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Options</h2>
            <div class="flex flex-wrap items-center gap-4 mt-2">
                <x-form.checkbox name="suspendbuffs" label="Suspend Buffs" :checked="$zone->suspendbuffs" />
                <x-form.checkbox name="hotzone" label="Hotzone" :checked="$zone->hotzone" />
                <x-form.checkbox name="bypass_expansion_check" label="Bypass Expansion Check" :checked="$zone->bypass_expansion_check" />
                <x-form.checkbox name="idle_when_empty" label="Idle When Empty" :checked="$zone->idle_when_empty" />
            </div>
        </div>
    </div>
</div>
