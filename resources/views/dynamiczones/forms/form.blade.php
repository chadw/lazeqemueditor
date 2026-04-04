<div class="space-y-6">
    <div class="grid grid-cols-3 gap-4">
        <x-form.input
            name="name"
            label="Name"
            x-model="$store.modalForm.form.name"
            wrapper-class="col-span-2"
        />
        <x-form.select
            name="type"
            label="Type"
            :options="config('everquest.dynamic_zone_type')"
            x-model="$store.modalForm.form.type"
        />
    </div>
    <div class="grid grid-cols-6 gap-4">
        <x-form.input
            name="instance_id"
            label="Instance ID"
            type="number"
            x-model="$store.modalForm.form.instance_id"
        />
        <div
            x-data='ajaxSelect({
                searchUrl: "/characters/search",
                useModal: true,
                prefillValue: () => $store.modalForm.form.leader ?? null,
                keyInOption: true,
                required: true,
            })'
            x-init="init()"
        >
            <label class="label">Leader</label>
            <select
                x-ref="select"
                name="leader_id"
                class="w-full validator invalid:select-error"
                required
            ></select>
        </div>
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
        <x-form.input
            name="dz_switch_id"
            label="DZ Switch ID"
            type="number"
            x-model="$store.modalForm.form.dz_switch_id"
        />
    </div>
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Options</h2>
            <div class="flex flex-wrap items-center gap-4">
                <x-form.checkbox
                    name="is_locked"
                    label="Is Locked"
                    x-model="$store.modalForm.form.is_locked"
                />
                <x-form.checkbox
                    name="add_replay"
                    label="Add Replay"
                    x-model="$store.modalForm.form.add_replay"
                />
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Safe Return</h2>
            <div class="grid grid-cols-2 gap-4">
                <x-form.select
                    name="safe_return_zone_id"
                    label="Zone"
                    :options="[0 => 'None'] + $zones"
                    x-model="$store.modalForm.form.safe_return_zone_id"
                />
                <div class="join">
                    <x-form.input
                        name="safe_return_x"
                        label="X"
                        class="join-item"
                        type="number"
                        step="any"
                        x-model="$store.modalForm.form.safe_return_x"
                    />
                    <x-form.input
                        name="safe_return_y"
                        label="Y"
                        class="join-item"
                        type="number"
                        step="any"
                        x-model="$store.modalForm.form.safe_return_y"
                    />
                    <x-form.input
                        name="safe_return_z"
                        label="Z"
                        class="join-item"
                        type="number"
                        step="any"
                        x-model="$store.modalForm.form.safe_return_z"
                    />
                    <x-form.input
                        name="safe_return_heading"
                        label="Heading"
                        class="join-item"
                        type="number"
                        step="any"
                        x-model="$store.modalForm.form.safe_return_heading"
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
                    :options="[0 => 'None'] + $zones"
                    x-model="$store.modalForm.form.compass_zone_id"
                />
                <div class="join">
                    <x-form.input
                        name="compass_x"
                        label="X"
                        class="join-item"
                        type="number"
                        step="any"
                        x-model="$store.modalForm.form.compass_x"
                    />
                    <x-form.input
                        name="compass_y"
                        label="Y"
                        class="join-item"
                        type="number"
                        step="any"
                        x-model="$store.modalForm.form.compass_y"
                    />
                    <x-form.input
                        name="compass_z"
                        label="Z"
                        class="join-item"
                        type="number"
                        step="any"
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
                    name="has_zone_in"
                    label="Has Zone In"
                    x-model="$store.modalForm.form.has_zone_in"
                />
                <div class="join">
                    <x-form.input
                        name="zone_in_x"
                        label="X"
                        class="join-item"
                        type="number"
                        step="any"
                        x-model="$store.modalForm.form.zone_in_x"
                    />
                    <x-form.input
                        name="zone_in_y"
                        label="Y"
                        class="join-item"
                        type="number"
                        step="any"
                        x-model="$store.modalForm.form.zone_in_y"
                    />
                    <x-form.input
                        name="zone_in_z"
                        label="Z"
                        class="join-item"
                        type="number"
                        step="any"
                        x-model="$store.modalForm.form.zone_in_z"
                    />
                    <x-form.input
                        name="zone_in_heading"
                        label="Heading"
                        class="join-item"
                        type="number"
                        step="any"
                        x-model="$store.modalForm.form.zone_in_heading"
                    />
                </div>
            </div>
        </div>
    </div>

    <template x-if="$store.modalForm.form?.members">
        <div class="card bg-base-200 card-sm shadow-sm">
            <div class="card-body">
                <h2 class="card-title">
                    Members (<span x-text="$store.modalForm.form.members.length"></span>)
                </h2>
                <div class="grid grid-cols-4 gap-4">
                    <template x-for="member in $store.modalForm.form.members" :key="member.id">
                        <div class="p-2 rounded bg-base-100 flex items-center justify-between gap-2">
                            <span x-text="member.character.name"></span>
                            <button type="button" class="btn btn-xs btn-soft btn-error"
                                @click="$store.ajaxRemover.remove({
                                    url: `/dynamic-zone-members/${member.id}`,
                                    id: member.id,
                                    arrayRef: $store.modalForm.form.members,
                                    arrayKey: 'id',
                                    confirmMessage: 'Remove this member from the dynamic zone?'
                                })">
                                <x-ui.icon name="delete" />
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </template>
</div>
