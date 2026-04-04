<div class="space-y-6">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-4 gap-4 mb-2">
                <template x-if="$store.modalForm.mode === 'create'">
                    <div x-cloak class="col-span-2">
                        <div
                            x-data="ajaxSelect({
                                searchUrl: '/characters/search',
                                prefillValue: () => $store.modalForm.form.character ?? null,
                                required: true,
                            })"
                            x-init="init()"
                        >
                            <label class="label">Character</label>
                            <select
                                x-ref="select"
                                name="character_id"
                                class="w-full">
                            </select>
                        </div>
                    </div>
                </template>
                <template x-if="$store.modalForm.mode === 'create'">
                    <div x-cloak>
                        <x-form.input
                            name="expedition_name"
                            label="Expedition Name"
                            x-model="$store.modalForm.form.expedition_name"
                        />
                    </div>
                </template>
                <template x-if="$store.modalForm.mode === 'create'">
                    <div x-cloak>
                        <x-form.input
                            name="from_expedition_uuid"
                            label="From Expedition UUID"
                            x-model="$store.modalForm.form.from_expedition_uuid"
                        />
                    </div>
                </template>
                <x-form.input
                    name="event_name"
                    label="Event Name"
                    x-model="$store.modalForm.form.event_name"
                    wrapper-class="col-span-2"
                />
                <x-form.input
                    name="expire_time"
                    label="Expire Time"
                    type="datetime-local"
                    step="1"
                    x-model="$store.modalForm.form._expires"
                />
                <div x-data="durationHelper()">
                    <x-form.input
                        name="duration"
                        label="Duration"
                        type="number"
                        min="0"
                        x-model="$store.modalForm.form.duration"
                        x-model.number="seconds"
                        x-bind:label-suffix="true"
                    />
                </div>
            </div>
        </div>
    </div>
</div>
