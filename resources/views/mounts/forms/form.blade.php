<div class="flex gap-6 items-stretch"
    x-data="{
        get raceModelKey() {
            const f = $store.modalForm.form
            return `${f.race ?? 0}-${f.gender ?? 0}-${f.texture ?? 0}-${f.helmtexture ?? 0}`
        },
        get isValidRaceModel() {
            return window.validRaceModels.has(this.raceModelKey)
        }
    }"
>
    <div class="space-y-6 flex-1" x-data="formTracker">
        <div class="card bg-base-200 card-sm shadow-sm">
            <div class="card-body">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <x-form.input
                        name="filename"
                        label="Name"
                        x-model="$store.modalForm.form.filename"
                        wrapper-class="col-span-2"
                        required
                    />
                    <x-form.select
                        name="race"
                        label="Race"
                        :options="config('everquest.db_races')"
                        keyInOption="true"
                        x-model="$store.modalForm.form.race"
                        wrapper-class="col-span-2"
                    />
                    <x-form.select
                        name="gender"
                        label="Gender"
                        :options="config('everquest.npc_genders')"
                        keyInOption="true"
                        x-model="$store.modalForm.form.gender"
                    />
                    <x-form.select
                        name="texture"
                        label="Texture"
                        :options="config('everquest.npc_textures')"
                        keyInOption="true"
                        x-model="$store.modalForm.form.texture"
                    />
                    <x-form.input
                        name="helmtexture"
                        label="Helm Texture"
                        x-model="$store.modalForm.form.helmtexture"
                    />
                    <x-form.input
                        name="mountspeed"
                        label="Speed"
                        type="number"
                        min="0"
                        step="0.01"
                        x-model="$store.modalForm.form.mountspeed"
                        @blur="$store.modalForm.form.mountspeed = parseFloat($store.modalForm.form.mountspeed).toFixed(2)"
                    />
                    <x-form.textarea
                        name="notes"
                        label=""
                        x-model="$store.modalForm.form.notes"
                        class="resize-none"
                        placeholder="Notes"
                        wrapper-class="col-span-4"
                    />
                </div>
            </div>
        </div>
    </div>
    <div class="w-28 shrink-0 flex rounded-lg bg-neutral/30 items-center justify-center">
        <div
            x-show="isValidRaceModel"
            class="race-model w-full h-full aspect-square transition-opacity duration-150"
            :class="`race-model-${raceModelKey}`"
        ></div>
        <div
            x-show="!isValidRaceModel"
            class="w-full h-full flex items-center justify-center text-xs text-neutral-content/50"
        >No Model</div>
    </div>
</div>
