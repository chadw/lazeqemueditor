<div class="card bg-base-200 card-sm shadow-sm mb-4">
    <div class="card-body">
        <div class="grid grid-cols-5 gap-4 mb-2">
            <x-form.input
                name="item_evolve_level"
                label="Level"
                x-model="$store.modalForm.form.item_evolve_level"
            />
            <div
                x-data="ajaxSelect({
                    searchUrl: '/items/search',
                    prefillValue: () => $store.modalForm.form.item ?? null,
                    allowNone: false,
                    required: true,
                })"
                x-init="init()"
                class="col-span-4"
            >
                <label class="label">Item</label>
                <select
                    x-ref="select"
                    name="item_id"
                    class="w-full validator invalid:select-error"
                    required
                ></select>
            </div>
        </div>
        <div class="grid grid-cols-4 gap-4">
            <x-form.select
                name="type"
                label="Type"
                :options="config('everquest.evolving_item_types')"
                x-model="$store.modalForm.form.type"
            />
            <div x-cloak x-show="$store.modalForm.form.type == 1" class="col-span-2">
                <x-form.select
                    name="sub_type"
                    label="Subtype (predefined)"
                    :options="config('everquest.evolving_item_subtypes')"
                    x-model="$store.modalForm.form.sub_type"
                    x-bind:disabled="$store.modalForm.form.type != 1"
                />
            </div>
            <div x-cloak x-show="$store.modalForm.form.type == 2" class="col-span-2">
                <x-form.input
                    name="sub_type"
                    label="Subtype (numeric)"
                    type="number"
                    x-model="$store.modalForm.form.sub_type"
                    x-bind:disabled="$store.modalForm.form.type != 2"
                />
            </div>
            <div x-cloak x-show="$store.modalForm.form.type == 3" class="col-span-2">
                <x-form.multi-select
                    name="sub_type[]"
                    label="Subtype (races)"
                    :options="config('everquest.db_races')"
                    x-model="$store.modalForm.form.sub_type"
                    x-bind:disabled="$store.modalForm.form.type != 3"
                    placeholder="Select races"
                />
            </div>
            <div x-cloak x-show="$store.modalForm.form.type == 4" class="col-span-2">
                <x-form.multi-select
                    name="sub_type[]"
                    label="Subtype (zones)"
                    :options="$zones"
                    x-model="$store.modalForm.form.sub_type"
                    x-bind:disabled="$store.modalForm.form.type != 4"
                    placeholder="Select zones"
                />
            </div>
            <x-form.input
                name="required_amount"
                label="Required Amount"
                x-model="$store.modalForm.form.required_amount"
            />
        </div>
        <template x-if="$store.modalForm.form.item_evo_id">
            <input type="hidden" name="item_evo_id" x-model="$store.modalForm.form.item_evo_id" />
        </template>
    </div>
</div>
