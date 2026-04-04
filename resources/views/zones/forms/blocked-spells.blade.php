<div class="space-y-6" x-data="formTracker">
    <input type="hidden" name="zoneid" x-model="$store.modalForm.form.zoneid" />
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-3 gap-4">
                <div
                    x-data="ajaxSelect({
                        searchUrl: '/spells/search',
                        prefillValue: () => $store.modalForm.form.spell ?? null,
                        required: true,
                    })"
                    x-init="init()"
                    class="col-span-2"
                >
                    <label class="label">Spell</label>
                    <select
                        x-ref="select"
                        name="spellid"
                        class="w-full"
                        required
                    ></select>
                </div>
                <x-form.select
                    name="type"
                    label="Type"
                    :options="config('everquest.blocked_spell_type')"
                    x-model="$store.modalForm.form.type"
                />
                <x-form.input
                    name="x"
                    label="X"
                    type="number"
                    step="any"
                    x-model="$store.modalForm.form.x"
                />
                <x-form.input
                    name="y"
                    label="Y"
                    type="number"
                    step="any"
                    x-model="$store.modalForm.form.y"
                />
                <x-form.input
                    name="z"
                    label="Z"
                    type="number"
                    step="any"
                    x-model="$store.modalForm.form.z"
                />
                <x-form.input
                    name="x_diff"
                    label="X Radius"
                    type="number"
                    step="any"
                    x-model="$store.modalForm.form.x_diff"
                />
                <x-form.input
                    name="y_diff"
                    label="Y Radius"
                    type="number"
                    step="any"
                    x-model="$store.modalForm.form.y_diff"
                />
                <x-form.input
                    name="z_diff"
                    label="Z Radius"
                    type="number"
                    step="any"
                    x-model="$store.modalForm.form.z_diff"
                />
                <x-form.input
                    name="message"
                    label="Message"
                    tooltip="Message when blocked"
                    maxlength="255"
                    x-model="$store.modalForm.form.message"
                    wrapper-class="col-span-3"
                />
                <x-form.input
                    name="description"
                    label="Description"
                    tooltip="Blocked spells description"
                    maxlength="255"
                    x-model="$store.modalForm.form.description"
                    wrapper-class="col-span-3"
                />
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
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
