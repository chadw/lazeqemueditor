<div class="space-y-6" x-data="formTracker">
    <input type="hidden" name="zoneid" :value="$store.modalForm.form.zoneid" />
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-6 gap-4">
                <div
                    x-data="ajaxSelect({
                        searchUrl: '/items/search',
                        prefillValue: () => $store.modalForm.form.item_ ?? null,
                        allowNone: true,
                        noneId: 0,
                        noneLabel: 'None',
                        required: false,
                    })"
                    x-init="init()"
                    class="col-span-2"
                >
                    <label class="label">Item</label>
                    <select
                        x-ref="select"
                        name="item"
                        class="w-full validator invalid:select-error"
                        required
                    ></select>
                </div>
                <x-form.input
                    name="version"
                    label="Version"
                    type="number"
                    min="0"
                    x-model="$store.modalForm.form.version"
                />
                <x-form.input
                    name="name"
                    label="Name"
                    x-model="$store.modalForm.form.name"
                />
                <x-form.input
                    name="max_allowed"
                    label="Max Allowed"
                    type="number"
                    min="1"
                    x-model="$store.modalForm.form.max_allowed"
                />
                <x-form.input
                    name="respawn_timer"
                    label="Respawn Timer"
                    type="number"
                    min="0"
                    x-model="$store.modalForm.form.respawn_timer"
                />
            </div>
        </div>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div class="card bg-base-200 card-sm shadow-sm">
            <div class="card-body">
                <h3 class="card-title">Max Coords</h3>
                <div class="grid grid-cols-4 gap-4">
                    <x-form.input
                        name="max_x"
                        label="X"
                        type="number"
                        step="any"
                        x-model="$store.modalForm.form.max_x"
                    />
                    <x-form.input
                        name="max_y"
                        label="Y"
                        type="number"
                        step="any"
                        x-model="$store.modalForm.form.max_y"
                    />
                    <x-form.input
                        name="max_z"
                        label="Z"
                        type="number"
                        step="any"
                        x-model="$store.modalForm.form.max_z"
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
                <h3 class="card-title">Min Coords</h3>
                <div class="grid grid-cols-3 gap-4">
                    <x-form.input
                        name="min_x"
                        label="X"
                        type="number"
                        step="any"
                        x-model="$store.modalForm.form.min_x"
                    />
                    <x-form.input
                        name="min_y"
                        label="Y"
                        type="number"
                        step="any"
                        x-model="$store.modalForm.form.min_y"
                    />
                </div>
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <x-form.input
                    name="comment"
                    label="Comment"
                    x-model="$store.modalForm.form.comment"
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
            <h3 class="card-title">Options</h3>
            <div class="flex flex-wrap items-center gap-4">
                <x-form.checkbox
                    name="fix_z"
                    label="Fix Z"
                    x-model="$store.modalForm.form.fix_z"
                />
            </div>
        </div>
    </div>
</div>
