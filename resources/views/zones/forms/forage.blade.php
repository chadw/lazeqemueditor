<div class="space-y-6" x-data="formTracker">
    <input type="hidden" name="zoneid" :value="$store.modalForm.form.zoneid" />
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-4 gap-4">
                <div
                    x-data="ajaxSelect({
                        searchUrl: '/items/search',
                        prefillValue: () => $store.modalForm.form.item ?? null,
                        required: true,
                    })"
                    x-init="init()"
                    class="col-span-2"
                >
                    <label class="label">Item</label>
                    <select
                        x-ref="select"
                        name="Itemid"
                        class="w-full validator invalid:select-error"
                        required
                    ></select>
                </div>
                <x-form.input
                    name="level"
                    label="Skill Level"
                    type="number"
                    min="0"
                    x-model="$store.modalForm.form.level"
                />
                <x-form.input
                    name="chance"
                    label="Chance %"
                    type="number"
                    min="0"
                    max="100"
                    x-model="$store.modalForm.form.chance"
                />
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
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
        </div>
    </div>
</div>
