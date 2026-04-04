<div class="space-y-6" x-data="formTracker">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-8 gap-4 mb-2">
                <div
                    x-data="ajaxSelect({
                        searchUrl: '/items/search',
                        prefillValue: () => $store.modalForm.form.item ?? null,
                    })"
                    x-init="init()"
                    class="col-span-4"
                >
                    <label class="label">Item</label>
                    <select
                        x-ref="select"
                        name="item_id"
                        class="w-full"
                    ></select>
                </div>
                <x-form.input
                    name="item_charges"
                    label="Charges"
                    type="number"
                    step="1"
                    min="0"
                    max="100"
                    x-model="$store.modalForm.form.item_charges"
                />
                <x-form.input
                    name="chance"
                    label="Chance %"
                    type="number"
                    tooltip="0 = Never, 100 = Always"
                    step="any"
                    min="0"
                    max="100"
                    x-model="$store.modalForm.form.chance"
                />
                <x-form.input
                    name="disabled_chance"
                    label="Disabled Chance %"
                    type="number"
                    tooltip="0 = Never, 100 = Always"
                    step="any"
                    min="0"
                    max="100"
                    x-model="$store.modalForm.form.disabled_chance"
                />
                <x-form.input
                    name="multiplier"
                    label="Multiplier"
                    type="number"
                    min="1"
                    max="100"
                    x-model="$store.modalForm.form.multiplier"
                />
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-2">
                <x-form.input
                    name="trivial_min_level"
                    label="Trivial Min Lvl"
                    type="number"
                    min="0"
                    x-model="$store.modalForm.form.trivial_min_level"
                />
                <x-form.input
                    name="trivial_max_level"
                    label="Trivial Max Lvl"
                    type="number"
                    min="0"
                    x-model="$store.modalForm.form.trivial_max_level"
                />
                <x-form.input
                    name="npc_min_level"
                    label="NPC Min Lvl"
                    type="number"
                    min="0"
                    x-model="$store.modalForm.form.npc_min_level"
                />
                <x-form.input
                    name="npc_max_level"
                    label="NPC Max Lvl"
                    type="number"
                    min="0"
                    x-model="$store.modalForm.form.npc_max_level"
                />
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-2">
                <x-form.select
                    name="min_expansion"
                    label="Min Expansion"
                    :options="[-1 => 'Any'] + config('everquest.expansions')"
                    x-model="$store.modalForm.form.min_expansion"
                />
                <x-form.select
                    name="max_expansion"
                    label="Max Expansion"
                    :options="[-1 => 'Any'] + config('everquest.expansions')"
                    x-model="$store.modalForm.form.max_expansion"
                />
                <x-form.content-flag-select
                    name="content_flags"
                    label="Content Flags"
                    x-model="$store.modalForm.form.content_flags"
                />
                <x-form.content-flag-select
                    name="content_flags_disabled"
                    label="Content Flags Disabled"
                    x-model="$store.modalForm.form.content_flags_disabled"
                />
            </div>
            <div class="flex flex-wrap items-center gap-4">
                <x-form.checkbox
                    name="equip_item"
                    label="Equip Item"
                    x-model="$store.modalForm.form.equip_item"
                />
            </div>
        </div>
    </div>
</div>
