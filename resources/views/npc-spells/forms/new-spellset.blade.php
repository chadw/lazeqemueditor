<div class="space-y-6" x-data="formTracker">
    <div class="card bg-base-200 card-sm shadow-sm mb-6">
        <div class="card-body">
            <h2 class="card-title">NPC Spell</h2>
            <input type="hidden" name="npc_id" x-model="$store.modalForm.form.npc_id" />
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <x-form.input
                    name="name"
                    label="Name"
                    tooltip=""
                    x-model="$store.modalForm.form.name"
                    wrapper-class="col-span-1 md:col-span-2"
                    required
                />
                <x-form.select
                    name="parent_list"
                    label="Parent"
                    tooltip=""
                    :options="[0 => 'None'] + $allNpcSpells"
                    x-model="$store.modalForm.form.parent_list"
                    keyInOption="true"
                />
                <x-form.input
                    name="fail_recast"
                    label="Fail Recast"
                    tooltip=""
                    type="number"
                    min="0"
                    x-model="$store.modalForm.form.fail_recast"
                />
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
        <div class="card bg-base-200 card-sm shadow-sm mb-6">
            <div class="card-body">
                <h2 class="card-title">Attack Proc</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div
                        x-data='ajaxSelect({
                            searchUrl: "/spells/search",
                            prefillValue: () => null,
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
                        x-model="$store.modalForm.form.proc_chance"
                    />
                </div>
            </div>
        </div>
        <div class="card bg-base-200 card-sm shadow-sm mb-6">
            <div class="card-body">
                <h2 class="card-title">Range Proc</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div
                        x-data='ajaxSelect({
                            searchUrl: "/spells/search",
                            prefillValue: () => null,
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
                        x-model="$store.modalForm.form.rproc_chance"
                    />
                </div>
            </div>
        </div>
        <div class="card bg-base-200 card-sm shadow-sm mb-6">
            <div class="card-body">
                <h2 class="card-title">Defensive Proc</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div
                        x-data='ajaxSelect({
                            searchUrl: "/spells/search",
                            prefillValue: () => null,
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
                        x-model="$store.modalForm.form.dproc_chance"
                    />
                </div>
            </div>
        </div>
    </div>
</div>
