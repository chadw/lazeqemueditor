<div class="card bg-base-200 card-sm shadow-sm mb-4">
    <div class="card-body">
        <div class="grid grid-cols-2 gap-4">
            <x-form.input
                name="type"
                label="Type"
                x-model="$store.modalForm.form.type"
                wrapper-class="col-span-2"
            />
            <div
                x-data="ajaxSelect({
                    searchUrl: '/npcs/search',
                    prefillValue: () => $store.modalForm.form.npc ?? null,
                    allowNone: true,
                    noneId: 0,
                })"
                x-init="init()"
            >
                <label class="label">Npc</label>
                <select
                    x-ref="select"
                    name="npcID"
                    class="w-full"
                ></select>
            </div>
            <x-form.input
                name="petpower"
                label="Pet Power"
                x-model="$store.modalForm.form.petpower"
            />
            <x-form.select
                name="petcontrol"
                label="Control"
                :options="config('everquest.pet_control')"
                x-model="$store.modalForm.form.petcontrol"
            />
            <x-form.select
                name="petnaming"
                label="Naming"
                :options="config('everquest.pet_naming')"
                x-model="$store.modalForm.form.petnaming"
            />
            <x-form.select
                name="equipmentset"
                label="Equipment Set"
                :options="['-1' => 'None'] + $petEquip"
                x-model="$store.modalForm.form.equipmentset"
            />
        </div>
    </div>
</div>

<div class="card bg-base-200 card-sm shadow-sm mb-4">
    <div class="card-body">
        <h2 class="card-title">Options</h2>
        <div class="flex flex-wrap items-center gap-4">
            <x-form.checkbox
                name="monsterflag"
                label="Monster Flag"
                tooltip=""
                x-model="$store.modalForm.form.monsterflag"
            />
            <x-form.checkbox
                name="temp"
                label="Temp"
                tooltip=""
                x-model="$store.modalForm.form.temp"
            />
        </div>
    </div>
</div>
