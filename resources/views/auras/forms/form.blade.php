<div class="space-y-6" x-data="formTracker">
    <div class="card bg-base-200 card-sm shadow-sm mb-5">
        <div class="card-body">
            <div class="grid grid-cols-6 gap-4">
                <x-form.input
                    name="name"
                    label="Name"
                    x-model="$store.modalForm.form.name"
                    wrapper-class="col-span-2"
                    required
                />
                <div
                    x-data="ajaxSelect({
                        searchUrl: '/npcs/search',
                        prefillValue: () => $store.modalForm.form.npc ?? null,
                    })"
                    x-init="init()"
                    class="col-span-2"
                >
                    <label class="label">NPC</label>
                    <select
                        x-ref="select"
                        name="npc_type"
                        class="w-full"
                        required
                    ></select>
                </div>
                <div
                    x-data="ajaxSelect({
                        searchUrl: '/spells/search',
                        prefillValue: () => $store.modalForm.form.spell ?? null,
                    })"
                    x-init="init()"
                    class="col-span-2"
                >
                    <label class="label">Spell</label>
                    <select
                        x-ref="select"
                        name="spell_id"
                        class="w-full"
                        required
                    ></select>
                </div>
                <x-form.select
                    name="aura_type"
                    label="Type"
                    :options="config('everquest.aura_type')"
                    x-model="$store.modalForm.form.aura_type"
                />
                <x-form.select
                    name="spawn_type"
                    label="Spawn Type"
                    :options="config('everquest.aura_spawn_types')"
                    x-model="$store.modalForm.form.spawn_type"
                />
                <x-form.select
                    name="movement"
                    label="Spawn Type"
                    :options="config('everquest.aura_movement_types')"
                    x-model="$store.modalForm.form.movement"
                />
                <x-form.input
                    name="distance"
                    label="Distance"
                    type="number"
                    min="0"
                    x-model="$store.modalForm.form.distance"
                />
                <div x-data="durationHelper()">
                    <x-form.input
                        name="duration"
                        label="Duration (s)"
                        type="number"
                        min="0"
                        x-model="$store.modalForm.form.duration"
                        x-model.number="seconds"
                        x-bind:label-suffix="true"
                    />
                </div>
                <x-form.input
                    name="icon"
                    label="Icon"
                    type="number"
                    min="-1"
                    x-model="$store.modalForm.form.icon"
                />
                <x-form.input
                    name="cast_time"
                    label="Cast Time"
                    type="number"
                    min="-1"
                    x-model="$store.modalForm.form.cast_time"
                />
            </div>
        </div>
    </div>
</div>
