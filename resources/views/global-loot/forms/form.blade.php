<div class="space-y-6">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-2 gap-4">
                <x-form.input
                    name="description"
                    label="Name"
                    x-model="$store.modalForm.form.description"
                    wrapper-class="col-span-2"
                    required
                />
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-5 gap-4">
                <x-form.input
                    name="min_level"
                    label="Min Level"
                    type="number"
                    min="0"
                    max="100"
                    x-model="$store.modalForm.form.min_level"
                    required
                />
                <x-form.input
                    name="max_level"
                    label="Max Level"
                    type="number"
                    min="0"
                    max="100"
                    x-model="$store.modalForm.form.max_level"
                    required
                />
                <x-form.select
                    name="hot_zone"
                    label="Hot Zone"
                    :options="[
                        -1 => 'Not Used',
                        0 => 'Must Not Be',
                        1 => 'Must Be'
                    ]"
                    x-model="$store.modalForm.form.hot_zone"
                />
                <x-form.select
                    name="rare"
                    label="Rare"
                    :options="[
                        '' => 'Not Used',
                        0 => 'Must Not Be',
                        1 => 'Must Be'
                    ]"
                    x-model="$store.modalForm.form.rare"
                />
                <x-form.select
                    name="raid"
                    label="Raid"
                    :options="[
                        '' => 'Not Used',
                        0 => 'Must Not Be',
                        1 => 'Must Be'
                    ]"
                    x-model="$store.modalForm.form.raid"
                />
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-2 gap-4">
                <x-form.multi-select
                    name="class"
                    label="Classes"
                    :options="config('everquest.classes_abbr')"
                    x-model="$store.modalForm.form._classes"
                    keyInOption=true
                    placeholder="All Classes"
                />
                <x-form.multi-select
                    name="race"
                    label="Races"
                    :options="config('everquest.db_races')"
                    x-model="$store.modalForm.form._races"
                    keyInOption=true
                    placeholder="All Races"
                />
                <x-form.multi-select
                    name="bodytype"
                    label="Body Type"
                    :options="config('everquest.db_bodytypes')"
                    x-model="$store.modalForm.form._bodytypes"
                    keyInOption=true
                    placeholder="All Body Types"
                />
                <x-form.multi-select
                    name="zone"
                    label="Zones"
                    :options="$zones"
                    x-model="$store.modalForm.form._zones"
                    keyInOption=true
                    placeholder="All Zones"
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
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Options</h2>
            <div class="flex flex-wrap items-center gap-4">
                <x-form.checkbox
                    name="enabled"
                    label="Enabled"
                    x-model="$store.modalForm.form.enabled"
                />
            </div>
        </div>
    </div>
</div>
