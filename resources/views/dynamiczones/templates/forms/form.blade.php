<div class="space-y-6">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-4 gap-4 mb-2">
                <x-form.input
                    name="name"
                    label="Name"
                    x-model="$store.modalForm.form.name"
                    wrapper-class="col-span-2"
                />
                <x-form.select
                    name="zone_id"
                    label="Zone"
                    x-model="$store.modalForm.form.zone_id"
                    x-data="selectHydrator({
                        url: '/zones/options',
                        valueKey: 'zoneidnumber',
                        labelKey: 'short_name',
                        allowEmpty: true,
                        get: () => $store.modalForm.form.zone_id,
                        getLabel: () => ($store.modalForm.form.zone && $store.modalForm.form.zone.short_name) ? $store.modalForm.form.zone.short_name : ($store.modalForm.form.zone_id ?? ''),
                    })"
                    x-init="setTimeout(() => load(), 0)"
                    x-on:mousedown="load()"
                    keyInOption="true"
                    required
                />
                <x-form.input
                    name="zone_version"
                    label="Zone Version"
                    type="number"
                    min="0"
                    x-model="$store.modalForm.form.zone_version"
                />
            </div>
            <div class="grid grid-cols-6 gap-4">
                <x-form.input
                    name="dz_switch_id"
                    label="DZ Switch ID"
                    type="number"
                    x-model="$store.modalForm.form.dz_switch_id"
                />
                <x-form.input
                    name="min_players"
                    label="Min Players"
                    type="number"
                    x-model="$store.modalForm.form.min_players"
                />
                <x-form.input
                    name="max_players"
                    label="Max Players"
                    type="number"
                    x-model="$store.modalForm.form.max_players"
                />
                <div x-data="durationHelper()">
                    <x-form.input
                        name="duration_seconds"
                        label="Duration"
                        type="number"
                        min="0"
                        x-model="$store.modalForm.form.duration_seconds"
                        x-model.number="seconds"
                        x-bind:label-suffix="true"
                    />
                </div>
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Safe Return</h2>
            <div class="grid grid-cols-2 gap-4">
                <x-form.select
                    name="return_zone_id"
                    label="Zone"
                    x-model="$store.modalForm.form.return_zone_id"
                    x-data="selectHydrator({
                        url: '/zones/options',
                        valueKey: 'zoneidnumber',
                        labelKey: 'short_name',
                        allowEmpty: true,
                        noneId: 0,
                        noneLabel: 'None',
                        get: () => $store.modalForm.form.return_zone_id,
                        getLabel: () => ($store.modalForm.form.return_zone && $store.modalForm.form.return_zone.short_name) ? $store.modalForm.form.return_zone.short_name : ($store.modalForm.form.return_zone_id ?? ''),
                    })"
                    x-init="setTimeout(() => load(), 0)"
                    x-on:mousedown="load()"
                    keyInOption="true"
                    required
                />
                <div class="join">
                    <x-form.input
                        name="return_x"
                        label="X"
                        class="join-item"
                        type="number"
                        x-model="$store.modalForm.form.return_x"
                    />
                    <x-form.input
                        name="return_y"
                        label="Y"
                        class="join-item"
                        type="number"
                        x-model="$store.modalForm.form.return_y"
                    />
                    <x-form.input
                        name="return_z"
                        label="Z"
                        class="join-item"
                        type="number"
                        x-model="$store.modalForm.form.return_z"
                    />
                    <x-form.input
                        name="return_h"
                        label="Heading"
                        class="join-item"
                        type="number"
                        x-model="$store.modalForm.form.return_h"
                    />
                </div>
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Compass</h2>
            <div class="grid grid-cols-2 gap-4">
                <x-form.select
                    name="compass_zone_id"
                    label="Zone"
                    x-model="$store.modalForm.form.compass_zone_id"
                    x-data="selectHydrator({
                        url: '/zones/options',
                        valueKey: 'zoneidnumber',
                        labelKey: 'short_name',
                        allowEmpty: true,
                        noneId: 0,
                        noneLabel: 'None',
                        get: () => $store.modalForm.form.compass_zone_id,
                        getLabel: () => ($store.modalForm.form.compass_zone && $store.modalForm.form.compass_zone.short_name) ? $store.modalForm.form.compass_zone.short_name : ($store.modalForm.form.compass_zone_id ?? ''),
                    })"
                    x-init="setTimeout(() => load(), 0)"
                    x-on:mousedown="load()"
                    keyInOption="true"
                    required
                />
                <div class="join">
                    <x-form.input
                        name="compass_x"
                        label="X"
                        class="join-item"
                        type="number"
                        x-model="$store.modalForm.form.compass_x"
                    />
                    <x-form.input
                        name="compass_y"
                        label="Y"
                        class="join-item"
                        type="number"
                        x-model="$store.modalForm.form.compass_y"
                    />
                    <x-form.input
                        name="compass_z"
                        label="Z"
                        class="join-item"
                        type="number"
                        x-model="$store.modalForm.form.compass_z"
                    />
                </div>
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Zone In</h2>
            <div class="grid grid-cols-2 gap-4">
                <x-form.checkbox
                    name="override_zone_in"
                    label="Override Zone In Location"
                    x-model="$store.modalForm.form.override_zone_in"
                />
                <div class="join">
                    <x-form.input
                        name="zone_in_x"
                        label="X"
                        class="join-item"
                        type="number"
                        x-model="$store.modalForm.form.zone_in_x"
                    />
                    <x-form.input
                        name="zone_in_y"
                        label="Y"
                        class="join-item"
                        type="number"
                        x-model="$store.modalForm.form.zone_in_y"
                    />
                    <x-form.input
                        name="zone_in_z"
                        label="Z"
                        class="join-item"
                        type="number"
                        x-model="$store.modalForm.form.zone_in_z"
                    />
                    <x-form.input
                        name="zone_in_h"
                        label="Heading"
                        class="join-item"
                        type="number"
                        x-model="$store.modalForm.form.zone_in_h"
                    />
                </div>
            </div>
        </div>
    </div>
</div>
