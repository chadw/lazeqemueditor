<div class="flex gap-6 items-stretch"
    x-data="{
        ...formTracker(),
        get raceModelKey() {
            const f = $store.modalForm.form
            return `${f.pet_race ?? 0}-${f.gender ?? 0}-${f.texture ?? 0}-${f.helm_texture ?? 0}`
        },
        get isValidRaceModel() {
            return window.validRaceModels.has(this.raceModelKey)
        }
    }"
>
    <div class="space-y-6 flex-1">
        <div class="card bg-base-200 card-sm shadow-sm">
            <div class="card-body">
                <div class="grid grid-cols-2 gap-4 mb-2">
                    <x-form.select
                        name="player_race"
                        label="Character Race"
                        :options="config('everquest.races')"
                        keyInOption="true"
                        x-model="$store.modalForm.form.player_race"
                    />
                    <x-form.select
                        name="pet_race"
                        label="Pet Race"
                        :options="config('everquest.db_races')"
                        keyInOption="true"
                        x-model="$store.modalForm.form.pet_race"
                    />
                </div>
                <div class="grid grid-cols-5 gap-4">
                    <x-form.input
                        name="texture"
                        label="Texture"
                        type="number"
                        min="0"
                        x-model="$store.modalForm.form.texture"
                    />
                    <x-form.input
                        name="helm_texture"
                        label="Helm Texture"
                        type="number"
                        min="0"
                        x-model="$store.modalForm.form.helm_texture"
                    />
                    <x-form.input
                        name="face"
                        label="Face"
                        type="number"
                        min="0"
                        x-model="$store.modalForm.form.face"
                    />
                    <x-form.select
                        name="gender"
                        label="Gender"
                        :options="[
                            0 => 'Male',
                            1 => 'Female',
                            2 => 'Neuter',
                        ]"
                        x-model="$store.modalForm.form.gender"
                    />
                    <x-form.input
                        name="size_modifier"
                        label="Size"
                        type="number"
                        min="0"
                        step="0.1"
                        x-model="$store.modalForm.form.size_modifier"
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
