<div class="space-y-6">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-4 mb-2">
                <div class="form-control w-full min-w-40">
                    <label class="label">
                        <span class="label-text">ID <span class="text-error">*</span></span>
                    </label>
                    <div x-data class="join w-full">
                        <input
                            id="npc_id_field" name="id" type="number"
                            value="{{ old('id', $npc->id) }}"
                            required
                            class="input w-full join-item flex-1 m-w-0"
                        />
                        <button
                            type="button"
                            class="btn btn-soft btn-secondary join-item flex-none"
                            @click='$store.idPicker.open({selector: "#npc_id_field", type: "npcs"})'
                        >
                            Pick
                        </button>
                    </div>
                </div>
                <x-form.input
                    name="name"
                    label="Name"
                    :value="$npc->name"
                    required
                    wrapper-class="col-span-1 sm:col-span-1 md:col-span-3 lg:col-span-2"
                />
                <x-form.input
                    name="lastname"
                    label="Title"
                    :value="$npc->lastname"
                    wrapper-class="col-span-1 sm:col-span-1 md:col-span-3 lg:col-span-2"
                />
                <x-form.select
                    name="gender"
                    label="Gender"
                    :options="[
                        0 => 'Male',
                        1 => 'Female',
                        2 => 'Neuter',
                    ]"
                    :selected="$npc->gender"
                />
                <x-form.input
                    name="level"
                    label="Level"
                    type="number"
                    min="0"
                    max="255"
                    :value="$npc->level"
                />
                <x-form.input
                    name="maxlevel"
                    label="Max Level"
                    type="number"
                    min="0"
                    max="255"
                    :value="$npc->maxlevel"
                />
                <x-form.select
                    name="race"
                    label="Race"
                    :options="config('everquest.db_races')"
                    :selected="$npc->race"
                    keyInOption="true"
                    wrapper-class="col-span-2"
                />
                <x-form.select
                    name="class"
                    label="Class"
                    :options="config('everquest.npc_class')"
                    :selected="$npc->class"
                    keyInOption="true"
                    wrapper-class="col-span-2"
                />
                <x-form.select
                    name="bodytype"
                    label="Body Type"
                    :options="config('everquest.db_bodytypes')"
                    :selected="$npc->bodytype"
                    keyInOption="true"
                    wrapper-class="col-span-2"
                />
                <x-form.input
                    name="version"
                    label="Version"
                    type="number"
                    min="0"
                    :value="$npc->version"
                />
                <x-form.input
                    name="spawn_limit"
                    label="Spawn Limit"
                    type="number"
                    min="0"
                    :value="$npc->spawn_limit"
                />
                <x-form.input
                    name="merchant_id"
                    label="Merchant ID"
                    type="number"
                    min="0"
                    :value="$npc->merchant_id"
                />
                <x-form.input
                    name="alt_currency_id"
                    label="Alt Currency Merchant ID"
                    type="number"
                    min="0"
                    :value="$npc->alt_currency_id"
                />
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Vitals</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-9 gap-4">
                <div x-data="numberHelper(0)">
                    <x-form.input
                        name="hp"
                        label="HP"
                        type="number"
                        min="0"
                        :value="$npc->hp"
                        x-bind:label-suffix="true"
                    />
                </div>
                <x-form.input
                    name="mana"
                    label="Mana"
                    type="number"
                    min="0"
                    :value="$npc->mana"
                />
                <x-form.input
                    name="AC"
                    label="AC"
                    type="number"
                    min="0"
                    :value="$npc->AC"
                />
                <x-form.input
                    name="Avoidance"
                    label="Avoidance"
                    type="number"
                    min="0"
                    :value="$npc->Avoidance"
                />
                <x-form.input
                    name="ATK"
                    label="Attack"
                    type="number"
                    min="0"
                    :value="$npc->ATK"
                />
                <x-form.input
                    name="Accuracy"
                    label="Accuracy"
                    type="number"
                    min="0"
                    :value="$npc->Accuracy"
                />
                <x-form.input
                    name="runspeed"
                    label="Run Speed"
                    type="number"
                    min="0"
                    step="0.01"
                    :value="$npc->runspeed"
                />
                <x-form.input
                    name="walkspeed"
                    label="Walk Speed"
                    type="number"
                    min="0"
                    step="0.01"
                    :value="$npc->walkspeed"
                />
                <x-form.input
                    name="scalerate"
                    label="Scale Rate"
                    type="number"
                    min="0"
                    :value="$npc->scalerate"
                />
            </div>
        </div>
    </div>
</div>
