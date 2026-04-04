<div class="space-y-6" x-data="formTracker">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h3 class="card-title">Origin Zone</h3>
            <div class="grid grid-cols-6 gap-4">
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
                    min="-1"
                    tooltip="-1 For All"
                    x-model="$store.modalForm.form.version"
                    required
                />
                {{--
                :options="$zone->custobjdata->pluck('object_name', 'object_name')->toArray()"
                x-model="$store.modalForm.form.name.toUpperCase()"
                --}}
                <x-form.input
                    name="name"
                    label="Name"
                    tooltip="This is the name of the door, such as 'IT11161' or 'POPCREATE501', for names of objects you can see."
                    x-model="$store.modalForm.form.name"
                />
                <x-form.input
                    name="doorid"
                    label="Door ID"
                    type="number"
                    min="0"
                    tooltip="Unique Door Identifier"
                    x-model="$store.modalForm.form.doorid"
                    required
                />
                <x-form.input
                    name="size"
                    label="Size"
                    x-model="$store.modalForm.form.size"
                />
                <x-form.input
                    name="incline"
                    label="Incline"
                    x-model="$store.modalForm.form.incline"
                />
                <x-form.input
                    name="door_param"
                    label="Door Parameter"
                    type="number"
                    x-model="$store.modalForm.form.door_param"
                />
                <x-form.input
                    name="pos_x"
                    label="X"
                    type="number"
                    step="any"
                    x-model="$store.modalForm.form.pos_x"
                />
                <x-form.input
                    name="pos_y"
                    label="Y"
                    type="number"
                    step="any"
                    x-model="$store.modalForm.form.pos_y"
                />
                <x-form.input
                    name="pos_z"
                    label="Z"
                    type="number"
                    step="any"
                    x-model="$store.modalForm.form.pos_z"
                />
                <x-form.input
                    name="heading"
                    label="Heading"
                    type="number"
                    tooltip="Door Heading Coordinate"
                    x-model="$store.modalForm.form.heading"
                />
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h3 class="card-title">Destination Zone</h3>
            <div class="grid grid-cols-8 gap-4">
                <x-form.select
                    name="dest_zone"
                    label="Zone"
                    x-model="$store.modalForm.form.dest_zone"
                    x-data="selectHydrator({
                        url: '/zones/options',
                        valueKey: 'short_name',
                        labelKey: 'short_name',
                        allowEmpty: true,
                        get: () => $store.modalForm.form.dest_zone,
                        getLabel: () => $store.modalForm.form.dest_zone,
                    })"
                    x-on:mousedown="load()"
                    wrapper-class="col-span-2"
                />
                <x-form.input
                    name="dest_instance"
                    label="Instance"
                    type="number"
                    min="0"
                    x-model="$store.modalForm.form.dest_instance"
                />
                <x-form.input
                    name="dest_x"
                    label="X"
                    type="number"
                    step="any"
                    x-model="$store.modalForm.form.dest_x"
                />
                <x-form.input
                    name="dest_y"
                    label="Y"
                    type="number"
                    step="any"
                    x-model="$store.modalForm.form.dest_y"
                />
                <x-form.input
                    name="dest_z"
                    label="Z"
                    type="number"
                    step="any"
                    x-model="$store.modalForm.form.dest_z"
                />
                <x-form.input
                    name="dest_heading"
                    label="Heading"
                    type="number"
                    step="any"
                    x-model="$store.modalForm.form.dest_heading"
                />
                <x-form.input
                    name="dz_switch_id"
                    label="DZ Switch ID"
                    type="number"
                    x-model="$store.modalForm.form.dz_switch_id"
                />
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-2 gap-4 mb-2">
                <x-form.select
                    name="opentype"
                    label="Open Type"
                    tooltip="115-152: All need invert_state to 1 to work correctly. If invert_state is 0, they don't move but you take damage still if you touch them (Thanks Qadar, for this information)"
                    :options="config('everquest.door_open_type')"
                    keyInOption=true
                    x-model="$store.modalForm.form.opentype"
                />
                <x-form.input
                    name="invert_state"
                    label="Invert State"
                    tooltip="This column will basically behave like such: if the door has a click type and it is to raise up like a door, it will be raised on spawn of the door. Meaning it is inverted. Another example: If a Door Open Type is set to a spinning object on click, you could set this to 1 to have the door be spinning on spawn."
                    type="number"
                    x-model="$store.modalForm.form.invert_state"
                />
            </div>
            <div class="grid grid-cols-6 gap-4 mb-2">
                <x-form.input
                    name="guild"
                    label="Guild"
                    type="number"
                    x-model="$store.modalForm.form.guild"
                />
                <x-form.input
                    name="lockpick"
                    label="Lockpick"
                    tooltip="Lockpicking Skill Required: -1 = Unpickable"
                    type="number"
                    min="-1"
                    x-model="$store.modalForm.form.lockpick"
                />
                <div
                    x-data="ajaxSelect({
                        searchUrl: '/items/search',
                        prefillValue: () => $store.modalForm.form.item ?? null,
                        allowNone: true,
                        noneId: 0,
                        noneLabel: 'None',
                        required: false,
                    })"
                    x-init="init()"
                >
                    <label class="label">Key Item</label>
                    <select
                        x-ref="select"
                        name="keyitem"
                        class="w-full validator invalid:select-error"
                        required
                    ></select>
                </div>
                <x-form.input
                    name="triggerdoor"
                    label="Trigger Door"
                    tooltip="0 For Current Door or use a Unique Door Identifier"
                    type="number"
                    min="0"
                    x-model="$store.modalForm.form.triggerdoor"
                />
                <x-form.input
                    name="triggertype"
                    label="Trigger Type"
                    tooltip="1 = Open a Type 255 door, 255 = Will Not Open"
                    type="number"
                    min="-1"
                    x-model="$store.modalForm.form.triggertype"
                />
                <x-form.input
                    name="close_timer_ms"
                    label="Close Timer (ms)"
                    type="number"
                    min="0"
                    x-model="$store.modalForm.form.close_timer_ms"
                />
                <x-form.input
                    name="buffer"
                    label="Buffer"
                    type="number"
                    min="0"
                    x-model="$store.modalForm.form.buffer"
                />
                <x-form.input
                    name="client_version_mask"
                    label="Client Version Mask"
                    type="number"
                    min="0"
                    x-model="$store.modalForm.form.client_version_mask"
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
            <h3 class="card-title">Options</h3>
            <div class="flex flex-wrap items-center gap-4">
                <x-form.checkbox
                    name="doorisopen"
                    label="Is Open"
                    tooltip=""
                    x-model="$store.modalForm.form.doorisopen"
                />
                <x-form.checkbox
                    name="nokeyring"
                    label="No keyring"
                    tooltip=""
                    x-model="$store.modalForm.form.nokeyring"
                />
                <x-form.checkbox
                    name="is_ldon_door"
                    label="Is LDoN Door"
                    tooltip=""
                    x-model="$store.modalForm.form.is_ldon_door"
                />
                <x-form.checkbox
                    name="disable_timer"
                    label="Disable Timer"
                    tooltip=""
                    x-model="$store.modalForm.form.disable_timer"
                />
            </div>
        </div>
    </div>
</div>
