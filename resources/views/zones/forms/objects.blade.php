<div class="space-y-6" x-data="formTracker">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-3 gap-4">
                <x-form.select
                    name="type"
                    label="Type"
                    :options="config('everquest.object_containers')"
                    x-model="$store.modalForm.form.type"
                />
                <div x-data="Object.assign(modelPreview($store.modalForm.form.objectname), { popoverOpen: false })"
                    class="join flex items-center gap-2"
                >
                    <x-form.input
                        name="objectname"
                        label="Object Name"
                        tooltip="This defines how an item will look when equiped or dropped on the ground."
                        x-model="$store.modalForm.form.objectname"
                        class="join-item input"
                        x-ref="input"
                        @input="updateFromInput($event.target.value)"
                    />
                    <button
                        type="button"
                        class="join-item btn btn-soft btn-secondary mt-4"
                        @click="$store.objectPicker.open('#objectname', {{ Js::from($objectIds) }}, { append: '_ACTORDEF' })"
                    >
                        <x-ui.icon name="search" />
                    </button>
                    <div class="relative inline-block">
                        <button
                            type="button"
                            class="join-item btn btn-soft btn-info mt-4"
                            @click="popoverOpen = !popoverOpen"
                            :aria-expanded="popoverOpen"
                        >
                            <x-ui.icon name="show" />
                        </button>
                        <div x-cloak x-show="popoverOpen" x-transition @click.outside="popoverOpen = false"
                            class="absolute z-50 top-full left-1/2 transform -translate-x-1/2 mt-1 w-auto"
                        >
                            <div class="card bg-neutral p-2 shadow-lg rounded-md relative">
                                <div class="relative w-31.25 h-31.25 rounded-lg border border-base-300
                                    bg-base-200 flex items-center justify-center overflow-hidden mx-auto"
                                >
                                    <template x-if="objectId">
                                        <div class="absolute inset-0 flex items-center justify-center p-2">
                                            <div :class="'object-icon object-' + objectId" class="max-w-full max-h-full"
                                                style="transform: scale(.9); transform-origin:center;"></div>
                                        </div>
                                    </template>
                                    <template x-if="!objectId">
                                        <div class="text-sm opacity-50">No Model</div>
                                    </template>
                                </div>
                            </div>
                            <div class="absolute left-1/2 transform -translate-x-1/2 bottom-full -mb-1 pointer-events-none">
                                <svg width="20" height="10" viewBox="0 0 20 10" class="block text-neutral">
                                    <path d="M0 10 L10 0 L20 10 Z" fill="currentColor" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- <x-form.input
                    name="objectname"
                    label="Name"
                    x-model="$store.modalForm.form.objectname"
                /> --}}
                <x-form.input
                    name="version"
                    label="Zone Version"
                    type="number"
                    tooltip="-1 For All"
                    x-model="$store.modalForm.form.version"
                />
                <div
                    x-data="ajaxSelect({
                        searchUrl: '/items/search',
                        prefillValue: () => $store.modalForm.form.item ?? null,
                        required: true,
                    })"
                    x-init="init()"
                    class="col-span-2"
                >
                    <label class="label">Item</label>
                    <select
                        x-ref="select"
                        name="itemid"
                        class="w-full validator invalid:select-error"
                        required
                    ></select>
                </div>
                <x-form.input
                    name="charges"
                    label="Charges"
                    type="number"
                    x-model="$store.modalForm.form.charges"
                />
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-5 gap-4 mb-2">
                <x-form.input
                    name="xpos"
                    label="X"
                    type="number"
                    step="any"
                    x-model="$store.modalForm.form.xpos"
                />
                <x-form.input
                    name="ypos"
                    label="Y"
                    type="number"
                    step="any"
                    x-model="$store.modalForm.form.ypos"
                />
                <x-form.input
                    name="zpos"
                    label="Z"
                    type="number"
                    step="any"
                    x-model="$store.modalForm.form.zpos"
                />
                <x-form.input
                    name="heading"
                    label="Heading"
                    type="number"
                    step="any"
                    x-model="$store.modalForm.form.heading"
                />
            </div>
            <div class="grid grid-cols-5 gap-4">
                <x-form.input
                    name="chance"
                    label="Chance %"
                    type="number"
                    min="0"
                    max="100"
                    tooltip="0 = None, 100 = Always"
                    x-model="$store.modalForm.form.chance"
                />
                <x-form.input
                    name="level"
                    label="Level"
                    type="number"
                    min="1"
                    max="100"
                    x-model="$store.modalForm.form.level"
                />
                <x-form.input
                    name="skill"
                    label="Skill Required"
                    type="number"
                    min="0"
                    x-model="$store.modalForm.form.skill"
                />
                <x-form.input
                    name="respawn_time"
                    label="Respawn (secs)"
                    type="number"
                    min="0"
                    tooltip="Respawn Timer in Seconds"
                    x-model="$store.modalForm.form.respawn_time"
                />
                <x-form.input
                    name="respawn_var"
                    label="Variance (secs)"
                    type="number"
                    min="0"
                    tooltip="Random Respawn Timer Variance in Seconds"
                    x-model="$store.modalForm.form.respawn_var"
                />
                <x-form.input
                    name="triggered_number"
                    label="Triggered Number"
                    type="number"
                    min="0"
                    x-model="$store.modalForm.form.triggered_number"
                />
                <x-form.input
                    name="group"
                    label="Group"
                    type="number"
                    min="0"
                    x-model="$store.modalForm.form.group"
                />
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <x-form.input
                    name="message"
                    label="Message"
                    x-model="$store.modalForm.form.message"
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
            <h2 class="card-title">Options</h2>
            <div class="flex flex-wrap items-center gap-4">
                <x-form.checkbox
                    name="despawn_when_triggered"
                    label="Despawn When Triggered"
                    x-model="$store.modalForm.form.despawn_when_triggered"
                />
                <x-form.checkbox
                    name="undetectable"
                    label="Undetectable"
                    x-model="$store.modalForm.form.undetectable"
                />
            </div>
        </div>
    </div>
</div>
