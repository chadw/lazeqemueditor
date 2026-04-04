<div class="space-y-6">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-3 gap-4 mb-2">
                <input
                    type="hidden"
                    name="npc_spells_id"
                    :value="$store.modalForm.form.npc_spells_id"
                />
                <div
                    x-data="ajaxSelect({
                        searchUrl: '/spells/search',
                        prefillValue: () => $store.modalForm.form.spells ?? null,
                        allowNone: true,
                        noneId: 0,
                    })"
                    x-init="init()"
                    class="col-span-2"
                >
                    <label class="label">Spell</label>
                    <select
                        x-ref="select"
                        name="spellid"
                        class="w-full"
                    ></select>
                </div>
                <x-form.select
                    name="type"
                    label="Type"
                    tooltip=""
                    :options="config('everquest.spell_types')"
                    x-model="$store.modalForm.form.type"
                />
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4 mb-2">
                <x-form.input
                    name="minlevel"
                    label="Min Level"
                    x-model="$store.modalForm.form.minlevel"
                />
                <x-form.input
                    name="maxlevel"
                    label="Max Level"
                    x-model="$store.modalForm.form.maxlevel"
                />
                <x-form.input
                    name="manacost"
                    label="Mana"
                    x-model="$store.modalForm.form.manacost"
                />
                <x-form.input
                    name="recast_delay"
                    label="Recast"
                    x-model="$store.modalForm.form.recast_delay"
                />
                <x-form.input
                    name="priority"
                    label="Priority"
                    x-model="$store.modalForm.form.priority"
                />
                <x-form.input
                    name="resist_adjust"
                    label="Resist Adjust"
                    x-model="$store.modalForm.form.resist_adjust"
                />
                <x-form.input
                    name="min_hp"
                    label="Min HP"
                    x-model="$store.modalForm.form.min_hp"
                />
                <x-form.input
                    name="max_hp"
                    label="Max HP"
                    x-model="$store.modalForm.form.max_hp"
                />
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
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
</div>
