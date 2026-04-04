<div x-data="() => ({ ...formTracker(), effect: $store.modalForm.form.effect })" class="space-y-6">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-3 gap-4">
                <x-form.select
                    name="effect"
                    label="Effect"
                    :options="[
                        0 => 'Spell Identifier',
                        1 => 'Radius',
                        2 => 'NPC Type Identifier',
                        3 => 'NPC Type Identifier',
                        4 => 'Minimum Damage',
                    ]"
                    x-model="$store.modalForm.form.effect"
                    x-on:change="effect = $store.modalForm.form.effect"
                />
                <template x-if="effect == 0">
                    <div x-cloak>
                        <div
                            x-data="ajaxSelect({
                                searchUrl: '/spells/search',
                                prefillValue: () => $store.modalForm.form.spell ?? null,
                                required: true,
                            })"
                            x-init="init()"
                        >
                            <label class="label">Spell</label>
                            <select
                                x-ref="select"
                                name="effectvalue"
                                class="w-full">
                            </select>
                        </div>
                    </div>
                </template>
                <template x-if="effect == 1 || effect == 4">
                    <div x-cloak>
                        <x-form.input
                            name="effectvalue"
                            label="Effect Value"
                            x-model="$store.modalForm.form.effectvalue"
                        />
                    </div>
                </template>
                <template x-if="effect == 2 || effect == 3">
                    <div x-cloak>
                        <div
                            x-data="ajaxSelect({
                                searchUrl: '/npcs/search',
                                prefillValue: () => $store.modalForm.form.npc ?? null,
                                required: true,
                            })"
                            x-init="init()"
                        >
                            <label class="label">NPC</label>
                            <select x-ref="select" name="effectvalue" class="w-full"></select>
                        </div>
                    </div>
                </template>
                <x-form.input
                    name="effectvalue2"
                    label="Effect Value 2"
                    x-model="$store.modalForm.form.effectvalue2"
                />
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-5 gap-4 mb-2">
                <x-form.input
                    name="x"
                    label="X"
                    type="number"
                    x-model="$store.modalForm.form.x"
                />
                <x-form.input
                    name="y"
                    label="Y"
                    type="number"
                    x-model="$store.modalForm.form.y"
                />
                <x-form.input
                    name="z"
                    label="Z"
                    type="number"
                    x-model="$store.modalForm.form.z"
                />
                <x-form.input
                    name="maxzdiff"
                    label="Max Z Diff"
                    type="number"
                    x-model="$store.modalForm.form.maxzdiff"
                />
                <x-form.input
                    name="radius"
                    label="Radius"
                    type="number"
                    min="0"
                    x-model="$store.modalForm.form.radius"
                />
            </div>
            <div class="grid grid-cols-5 gap-4">
                <x-form.input
                    name="version"
                    label="Version"
                    type="number"
                    tooltip="-1 For All"
                    x-model="$store.modalForm.form.version"
                />
                <x-form.input
                    name="chance"
                    label="Chance %"
                    type="number"
                    min="0"
                    max="100"
                    tooltip="0 = None, 100 = Always"
                    x-model="$store.modalForm.form.chance"
                />
                <x-form.input
                    name="level"
                    label="Level"
                    type="number"
                    min="1"
                    max="100"
                    x-model="$store.modalForm.form.level"
                />
                <x-form.input
                    name="skill"
                    label="Skill Required"
                    type="number"
                    min="0"
                    x-model="$store.modalForm.form.skill"
                />
                <x-form.input
                    name="respawn_time"
                    label="Respawn (secs)"
                    type="number"
                    min="0"
                    tooltip="Respawn Timer in Seconds"
                    x-model="$store.modalForm.form.respawn_time"
                />
                <x-form.input
                    name="respawn_var"
                    label="Variance (secs)"
                    type="number"
                    min="0"
                    tooltip="Random Respawn Timer Variance in Seconds"
                    x-model="$store.modalForm.form.respawn_var"
                />
                <x-form.input
                    name="triggered_number"
                    label="Triggered Number"
                    type="number"
                    min="0"
                    x-model="$store.modalForm.form.triggered_number"
                />
                <x-form.input
                    name="group"
                    label="Group"
                    type="number"
                    min="0"
                    x-model="$store.modalForm.form.group"
                />
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <x-form.input
                    name="message"
                    label="Message"
                    x-model="$store.modalForm.form.message"
                    wrapper-class="col-span-2"
                />
                <x-form.select
                    name="min_expansion"
                    label="Min Expansion"
                    :options="[-1 => 'Any'] + config('everquest.expansions')"
                    x-model="$store.modalForm.form.min_expansion"
                />
                <x-form.select
                    name="max_expansion"
                    label="Max Expansion"
                    :options="[-1 => 'Any'] + config('everquest.expansions')"
                    x-model="$store.modalForm.form.max_expansion"
                />
                <x-form.content-flag-select
                    name="content_flags"
                    label="Content Flags"
                    x-model="$store.modalForm.form.content_flags"
                />
                <x-form.content-flag-select
                    name="content_flags_disabled"
                    label="Content Flags Disabled"
                    x-model="$store.modalForm.form.content_flags_disabled"
                />
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Options</h2>
            <div class="flex flex-wrap items-center gap-4">
                <x-form.checkbox
                    name="despawn_when_triggered"
                    label="Despawn When Triggered"
                    x-model="$store.modalForm.form.despawn_when_triggered"
                />
                <x-form.checkbox
                    name="undetectable"
                    label="Undetectable"
                    x-model="$store.modalForm.form.undetectable"
                />
            </div>
        </div>
    </div>
</div>
