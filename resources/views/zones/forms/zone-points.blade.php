<div class="space-y-6" x-data="formTracker">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h3 class="card-title">Origin Zone</h3>
            <div class="grid grid-cols-5 gap-4">
                <x-form.select
                    name="zone"
                    label="Zone"
                    x-model="$store.modalForm.form.zone"
                    x-data="selectHydrator({
                        url: '/zones/options',
                        valueKey: 'short_name',
                        labelKey: 'short_name',
                        allowEmpty: false,
                        get: () => $store.modalForm.form.zone,
                        getLabel: () => $store.modalForm.form.zone,
                    })"
                    x-on:mousedown="load()"
                    wrapper-class="col-span-2"
                />
                <x-form.input
                    name="version"
                    label="Version"
                    type="number"
                    min="0"
                    x-model="$store.modalForm.form.version"
                    required
                />
                <x-form.input
                    name="number"
                    label="Number"
                    type="number"
                    min="0"
                    x-model="$store.modalForm.form.number"
                    required
                />
                <x-form.input
                    name="zoneinst"
                    label="Zone Instance"
                    type="number"
                    min="0"
                    x-model="$store.modalForm.form.zoneinst"
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
                    name="heading"
                    label="Heading"
                    type="number"
                    step="any"
                    x-model="$store.modalForm.form.heading"
                />
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h3 class="card-title">Target Zone</h3>
            <div class="grid grid-cols-8 gap-4">
                <x-form.select
                    name="target_zone_id"
                    label="Zone"
                    x-model="$store.modalForm.form.target_zone_id"
                    x-data="selectHydrator({
                        url: '/zones/options',
                        valueKey: 'zoneidnumber',
                        labelKey: 'short_name',
                        allowEmpty: true,
                        get: () => $store.modalForm.form.target_zone_id,
                        getLabel: () => $store.modalForm.form.target_zones.short_name,
                    })"
                    x-on:mousedown="load()"
                    wrapper-class="col-span-3"
                />
                <x-form.input
                    name="target_instance"
                    label="Instance"
                    type="number"
                    min="0"
                    x-model="$store.modalForm.form.target_instance"
                />
                <x-form.input
                    name="target_x"
                    label="X"
                    type="number"
                    step="any"
                    x-model="$store.modalForm.form.target_x"
                />
                <x-form.input
                    name="target_y"
                    label="Y"
                    type="number"
                    step="any"
                    x-model="$store.modalForm.form.target_y"
                />
                <x-form.input
                    name="target_z"
                    label="Z"
                    type="number"
                    step="any"
                    x-model="$store.modalForm.form.target_z"
                />
                <x-form.input
                    name="target_heading"
                    label="Heading"
                    type="number"
                    step="any"
                    x-model="$store.modalForm.form.target_heading"
                />
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-3 gap-4 mb-2">
                <x-form.input
                    name="buffer"
                    label="Buffer"
                    tooltip="Unknown what this field does."
                    type="number"
                    x-model="$store.modalForm.form.buffer"
                />
                <x-form.input
                    name="width"
                    label="Width"
                    tooltip="Unknown what this field does."
                    type="number"
                    x-model="$store.modalForm.form.width"
                />
                <x-form.input
                    name="height"
                    label="Height"
                    tooltip="Unknown what this field does."
                    type="number"
                    x-model="$store.modalForm.form.height"
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
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Options</h2>
            <div class="flex flex-wrap items-center gap-4">
                <x-form.checkbox
                    name="is_virtual"
                    label="Is Virtual"
                    tooltip=""
                    x-model="$store.modalForm.form.is_virtual"
                />
            </div>
        </div>
    </div>
</div>
