<div x-data="{ effect: $store.modalForm.form.effect }" class="space-y-4">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h2 class="card-title">LDoN Trap Template</h2>
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
                    :selected="$trapTemplate->type ?? null"
                />
                <div
                    x-data='ajaxSelect({
                        searchUrl: "/spells/search",
                        useModal: false,
                        prefillValue: @json($trapTemplate->spell)
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
                    :selected="$trapTemplate->skill ?? null"
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
                    tooltip=""
                    :checked="$trapTemplate->locked"
                />
            </div>
        </div>
    </div>
    <div class="gap-4 text-right">
        <button type="submit" class="btn btn-soft btn-success">Save LDon Trap Template</button>
    </div>
</div>
