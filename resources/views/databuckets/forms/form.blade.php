<div class="space-y-6" x-data="formTracker">
    <div class="card bg-base-200 card-sm shadow-sm mb-4">
        <div class="card-body">
            <div class="grid grid-cols-6 gap-4">
                <x-form.input
                    name="key"
                    label="Key"
                    required
                    x-model="$store.modalForm.form.key"
                    wrapper-class="col-span-4"
                />
                <x-form.input
                    name="value"
                    label="Value"
                    x-model="$store.modalForm.form.value"
                />
                <x-form.input
                    name="expires"
                    label="Expires"
                    x-model="$store.modalForm.form.expires"
                />
                <div
                    x-data="ajaxSelect({
                        searchUrl: '/accounts/search',
                        prefillValue: () => $store.modalForm.form.account ?? null,
                        allowNone: true,
                        noneId: 0,
                    })"
                    x-init="init()"
                    class="col-span-2"
                >
                    <label class="label">Account</label>
                    <select
                        x-ref="select"
                        name="account_id"
                        class="w-full validator invalid:select-error"

                    ></select>
                </div>
                <div
                    x-data="ajaxSelect({
                        searchUrl: '/characters/search',
                        prefillValue: () => $store.modalForm.form.character ?? null,
                        allowNone: true,
                        noneId: 0,
                    })"
                    x-init="init()"
                    class="col-span-2"
                >
                    <label class="label">Character</label>
                    <select
                        x-ref="select"
                        name="character_id"
                        class="w-full validator invalid:select-error"
                    ></select>
                </div>
                <div
                    x-data="ajaxSelect({
                        searchUrl: '/npcs/search',
                        prefillValue: () => $store.modalForm.form.npc ?? null,
                        allowNone: true,
                        noneId: 0,
                    })"
                    x-init="init()"
                    class="col-span-2"
                >
                    <label class="label">NPC</label>
                    <select
                        x-ref="select"
                        name="npc_id"
                        class="w-full validator invalid:select-error"
                    ></select>
                </div>
                <x-form.input
                    name="bot_id"
                    label="Bot ID"
                    type="number"
                    min="0"
                    x-model="$store.modalForm.form.bot_id"
                    wrapper-class="col-span-2"
                />
                <x-form.select
                    name="zone"
                    label="Zone"
                    x-model="$store.modalForm.form.zone_id"
                    x-data="selectHydrator({
                        url: '/zones/options',
                        valueKey: 'zoneidnumber',
                        labelKey: 'short_name',
                        allowEmpty: true,
                        noneId: 0,
                        noneLabel: 'None',
                        get: () => $store.modalForm.form.zone,
                        getLabel: () => $store.modalForm.form.zone,
                    })"
                    x-on:mousedown="load()"
                    keyInOption="true"
                    wrapper-class="col-span-2"
                />
                <x-form.input
                    name="instance_id"
                    label="Instance ID"
                    type="number"
                    min="0"
                    x-model="$store.modalForm.form.instance_id"
                    wrapper-class="col-span-2"
                />
            </div>
        </div>
    </div>
</div>
