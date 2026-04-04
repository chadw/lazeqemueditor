<div
    x-data="{
        ...factionModForm(),
        ...formTracker(),
        init() {
            factionModForm().init.call(this);
            formTracker().init.call(this);
        }
    }" class="space-y-4">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-3 gap-4">
                <div class="form-control w-full">
                    <label for="_mod_type" class="block text-sm font-medium">Mod Type</label>
                    <select id="_mod_type" name="_mod_type" x-model="type" @change="modValue = null" class="select w-full border">
                        <option value="">Select Type</option>
                        <option value="class">Class</option>
                        <option value="race">Race</option>
                        <option value="deity">Deity</option>
                    </select>
                </div>

                <div x-show="type" x-cloak>
                    <label for="_mod_value" class="block text-sm font-medium">Type Value</label>
                    <select id="_mod_value" name="_mod_value" x-model="modValue" class="select w-full border">
                        <option value="">Select Value...</option>
                        <template x-for="(label, key) in options()" :key="key">
                            <option :value="key" x-text="Number(key) + ': ' + label"></option>
                        </template>
                    </select>
                </div>

                <input type="hidden" name="mod_name" :value="encoded()" />

                <x-form.input
                    name="mod"
                    label="Mod"
                    type="number"
                    min="-2000"
                    max="2000"
                    x-model="$store.modalForm.form.mod"
                    required
                />
            </div>
        </div>
    </div>
</div>
