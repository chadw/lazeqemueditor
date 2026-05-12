<div class="space-y-6">
    <input type="hidden" name="taskid" :value="$store.modalForm.form.taskid" />
    <input type="hidden" name="activityid" :value="$store.modalForm.form.activityid" />
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-7 gap-4 mb-2">
                <x-form.select
                    name="activitytype"
                    label="Type"
                    :options="config('everquest.task_activity_type')"
                    keyInOption=true
                    tooltip="This is the type of task activity, kill, loot etc."
                    x-model="$store.modalForm.form.activitytype"
                />
                <x-form.input
                    name="step"
                    label="Step"
                    type="number"
                    min="0"
                    tooltip="This is the logical step of your task activity, you can have many activities in one step, you have to complete all activities in one step to unlock the next step"
                    x-model="$store.modalForm.form.step"
                />
                <x-form.input
                    name="goalcount"
                    label="Goal Count"
                    type="number"
                    min="0"
                    tooltip="Required count of this activity to be completed."
                    x-model="$store.modalForm.form.goalcount"
                />
                <div class="form-control w-full">
                    <label class="label" for="req_activity_id">
                        <span class="label-text">Required Activity</span>
                    </label>
                    <select
                        name="req_activity_id"
                        x-model="$store.modalForm.form.req_activity_id"
                        class="select w-full"
                    >
                        <option value="-1">None</option>
                        <template x-for="opt in $store.modalForm.meta.reqActivityOptions" :key="opt.value">
                            <option
                                :value="opt.value"
                                :selected="opt.value == $store.modalForm.form.req_activity_id"
                                x-text="opt.label">
                            </option>
                        </template>
                    </select>
                </div>
                <x-form.input
                    name="list_group"
                    label="List Group"
                    type="number"
                    min="0"
                    x-model="$store.modalForm.form.list_group"
                />
            </div>
            <div class="grid grid-cols-8 gap-4 mb-2">
                <x-form.input
                    name="target_name"
                    label="Target Name"
                    x-model="$store.modalForm.form.target_name"
                    wrapper-class="col-span-3"
                />
                <x-form.input
                    name="description_override"
                    label="Description Override"
                    x-model="$store.modalForm.form.description_override"
                    wrapper-class="col-span-5"
                />
                <x-form.input
                    name="item_list"
                    label="Item Name"
                    x-model="$store.modalForm.form.item_list"
                    wrapper-class="col-span-3"
                />
                <div
                    x-data='ajaxSelect({
                        searchUrl: "/zones/search",
                        prefillValue: () => $store.modalForm.form.zone ?? null,
                        allowNone: true,
                        noneId: 0,
                        multiple: true,
                        delimiter: ";",
                    })'
                    x-init="init()"
                    class="col-span-4"
                >
                    <label class="label">Zones</label>
                    <select
                        x-ref="select"
                        name="zones[]"
                        multiple
                        class="w-full validator invalid:select-error"
                        tooltip=""
                    ></select>
                </div>
                <x-form.input
                    name="zone_version"
                    label="Zone Version"
                    type="number"
                    min="-1"
                    x-model="$store.modalForm.form.zone_version"
                    wrapper-class="col-span-1"
                />
                <div
                    x-show="
                        $store.modalForm.form.activitytype == 1 ||
                        $store.modalForm.form.activitytype == 3 ||
                        $store.modalForm.form.activitytype == 6 ||
                        $store.modalForm.form.activitytype == 7 ||
                        $store.modalForm.form.activitytype == 8
                    "
                    x-cloak
                    x-data='ajaxSelect({
                        searchUrl: "/items/search",
                        prefillValue: () => $store.modalForm.form.items ?? null,
                        allowNone: true,
                        noneId: 0,
                        multiple: true,
                    })'
                    x-init="init()"
                    class="col-span-8"
                >
                    <label class="label">Item Match List (items to be looted)</label>
                    <select
                        x-ref="select"
                        name="item_id_list[]"
                        multiple
                        class="w-full validator invalid:select-error"
                        tooltip=""
                    ></select>
                </div>
                <div
                    x-show="
                        $store.modalForm.form.activitytype == 1 ||
                        $store.modalForm.form.activitytype == 2 ||
                        $store.modalForm.form.activitytype == 4 ||
                        $store.modalForm.form.activitytype == 100
                    "
                    x-cloak
                    x-data='ajaxSelect({
                        searchUrl: "/npcs/search",
                        prefillValue: () => $store.modalForm.form.npcs ?? null,
                        allowNone: true,
                        noneId: 0,
                        multiple: true,
                    })'
                    x-init="init()"
                    class="col-span-8"
                >
                    <label class="label">NPC Match List (To be killed)</label>
                    <select
                        x-ref="select"
                        name="npc_match_list[]"
                        multiple
                        class="w-full validator invalid:select-error"
                        tooltip=""
                    ></select>
                </div>
            </div>
        </div>
    </div>
    <div
        x-show="$store.modalForm.form.activitytype == 5"
        x-cloak
        class="card bg-base-200 card-sm shadow-sm"
    >
        <div class="card-body">
            <h3 class="card-title">Proximities</h3>
            <div class="grid grid-cols-6 gap-4">
                <x-form.input
                    name="min_x"
                    label="Min X"
                    type="number"
                    tooltip=""
                    x-model="$store.modalForm.form.min_x"
                />
                <x-form.input
                    name="min_y"
                    label="Min Y"
                    type="number"
                    tooltip=""
                    x-model="$store.modalForm.form.min_y"
                />
                <x-form.input
                    name="min_z"
                    label="Min Z"
                    type="number"
                    tooltip=""
                    x-model="$store.modalForm.form.min_z"
                />
                <x-form.input
                    name="max_x"
                    label="Max X"
                    type="number"
                    tooltip=""
                    x-model="$store.modalForm.form.max_x"
                />
                <x-form.input
                    name="max_y"
                    label="Max Y"
                    type="number"
                    tooltip=""
                    x-model="$store.modalForm.form.max_y"
                />
                <x-form.input
                    name="max_z"
                    label="Max Z"
                    type="number"
                    tooltip=""
                    x-model="$store.modalForm.form.max_z"
                />
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h3 class="card-title">Options</h3>
            <div class="flex flex-wrap items-center gap-4">
                <x-form.checkbox
                    name="optional"
                    label="Optional"
                    x-model="$store.modalForm.form.optional"
                />
            </div>
        </div>
    </div>
</div>
