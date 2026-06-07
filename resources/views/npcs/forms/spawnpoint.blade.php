<div class="space-y-6" x-data="formTracker">
    <div class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="badge badge-soft badge-info">Spawn Group:
                #<span x-text="$store.modalForm.form.spawngroupID"></span>
            </div>
            <div class="badge badge-soft">Zone: <span class="font-mono"
                x-text="$store.modalForm.form.zone || $store.modalForm.form.spawn?.zone || '-'"></span>
            </div>
        </div>
    </div>
    <input type="hidden" name="spawngroupID" x-model="$store.modalForm.form.spawngroupID" />
    @php
        $zoneOptions = $zones->mapWithKeys(function ($z) {
            return [$z->short_name => $z->short_name . ': ' . $z->long_name];
        })->all();
    @endphp
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-8 gap-4">
                <x-form.select
                    name="zone"
                    label="Zone"
                    :options="$zoneOptions"
                    x-model="$store.modalForm.form.zone"
                    wrapper-class="col-span-7"
                />
                <x-form.input
                    name="version"
                    label="Zone Version"
                    type="number"
                    min="-1"
                    x-model="$store.modalForm.form.version"
                />
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-6 gap-4">
                <div x-data="durationHelper()">
                    <x-form.input
                        name="respawntime"
                        label="Respawn Time"
                        type="number"
                        min="0"
                        x-model="$store.modalForm.form.respawntime"
                        x-model.number="seconds"
                        x-bind:label-suffix="true"
                    />
                </div>
                <div x-data="durationHelper()">
                    <x-form.input
                        name="variance"
                        label="Variance"
                        type="number"
                        min="0"
                        x-model="$store.modalForm.form.variance"
                        x-model.number="seconds"
                        x-bind:label-suffix="true"
                    />
                </div>
                <x-form.input
                    name="pathgrid"
                    label="Grid Path"
                    type="number"
                    min="0"
                    x-model="$store.modalForm.form.pathgrid"
                />
                <x-form.input
                    name="_condition"
                    label="Condition"
                    type="number"
                    min="0"
                    x-model="$store.modalForm.form._condition"
                />
                <x-form.input
                    name="cond_value"
                    label="Condition Value"
                    type="number"
                    min="0"
                    x-model="$store.modalForm.form.cond_value"
                />
                <x-form.select
                    name="animation"
                    label="Animation"
                    :options="config('everquest.npc_animation_types')"
                    x-model="$store.modalForm.form.animation"
                />
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Loc</h2>
            <div class="grid grid-cols-4 gap-4">
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
                    name="disabled"
                    label="Disabled"
                    x-model="$store.modalForm.form.disabled"
                />
                <x-form.checkbox
                    name="path_when_zone_idle"
                    label="Path When Zone Idle"
                    x-model="$store.modalForm.form.path_when_zone_idle"
                />
            </div>
        </div>
    </div>
</div>
