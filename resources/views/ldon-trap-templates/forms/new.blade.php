<div class="space-y-6" x-data="{ ...formTracker(), effect: $store.modalForm.form.effect }">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-3 gap-4">
                <x-form.select
                    name="type"
                    label="Type"
                    :options="[
                        0 => 'Spell Identifier',
                        1 => 'Radius',
                        2 => 'NPC Type Identifier',
                        3 => 'NPC Type Identifier',
                        4 => 'Minimum Damage',
                    ]"
                    keyInOption="true"
                    x-model="$store.modalForm.form.type"
                />
                <div
                    x-data='ajaxSelect({
                        searchUrl: "/spells/search",
                        useModal: false,
                        prefillValue: () => $store.modalForm.form.spell ?? null,
                    })'
                    x-init="init()"
                >
                    <label class="label">Spell</label>
                    <select
                        x-ref="select"
                        name="spell_id"
                        class="w-full"
                    ></select>
                </div>
                <x-form.select
                    name="skill"
                    label="Skill"
                    :options="config('everquest.db_skills')"
                    keyInOption="true"
                    x-model="$store.modalForm.form.skill"
                />
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h3 class="card-title">Options</h3>
            <div class="flex flex-wrap items-center gap-4">
                <x-form.checkbox
                    name="locked"
                    label="Locked"
                    x-model="$store.modalForm.form.locked"
                />
            </div>
        </div>
    </div>
</div>
