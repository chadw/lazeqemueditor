<div class="card bg-base-200 card-sm shadow-sm mb-4">
    <div class="card-body">
        <div class="grid grid-cols-4 gap-4">
            <input type="hidden" name="old_name" x-model="$store.modalForm.form.name">
            <input type="hidden" name="old_charid" x-model="$store.modalForm.form.charid">
            <input type="hidden" name="old_npcid" x-model="$store.modalForm.form.npcid">
            <input type="hidden" name="old_zoneid" x-model="$store.modalForm.form.zoneid">

            <x-form.input
                name="name"
                label="Name"
                x-model="$store.modalForm.form.name"
                wrapper-class="col-span-2"
            />
            <x-form.input
                name="value"
                label="Value"
                x-model="$store.modalForm.form.value"
            />
            <x-form.input
                name="expdate"
                label="Expires"
                type="datetime-local"
                x-model="$store.modalForm.form.expdate"
            />
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div
                x-data="ajaxSelect({
                    searchUrl: '/characters/search',
                    prefillValue: () => $store.modalForm.form.character ?? null,
                    allowNone: true,
                    noneId: 0,
                    noneLabel: 'None',
                })"
                x-init="init()"
            >
                <label class="label">Character</label>
                <select
                    x-ref="select"
                    name="charid"
                    class="w-full"
                ></select>
            </div>
            <div
                x-data="ajaxSelect({
                    searchUrl: '/npcs/search',
                    prefillValue: () => $store.modalForm.form.npc ?? null,
                    allowNone: true,
                    noneId: 0,
                    noneLabel: 'None',
                })"
                x-init="init()"
            >
                <label class="label">Npc</label>
                <select
                    x-ref="select"
                    name="npcid"
                    class="w-full"
                ></select>
            </div>
            <x-form.select
                name="zoneid"
                label="Zone"
                :options="[0 => 'None'] + $zones"
                x-model="$store.modalForm.form.zoneid"
            />
        </div>
    </div>
</div>
