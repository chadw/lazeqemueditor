<div class="space-y-6">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-6 gap-4">
                <input type="hidden" name="slot" :value="$store.modalForm.form.slot" />
                <div
                    x-data="ajaxSelect({
                        searchUrl: '/items/search',
                        prefillValue: () => $store.modalForm.form.items ?? null,
                        allowNone: false,
                        required: true,
                    })"
                    x-init="init()"
                    class="col-span-2"
                >
                    <label class="label">Item</label>
                    <select
                        x-ref="select"
                        name="item"
                        class="w-full validator invalid:select-error"
                        required
                    ></select>
                </div>
                <x-form.input
                    name="alt_currency_cost"
                    label="Alt Currency Cost"
                    type="number"
                    min="0"
                    x-model="$store.modalForm.form.alt_currency_cost"
                />
                <x-form.input
                    name="faction_required"
                    label="Faction Required"
                    type="number"
                    min="-2000"
                    max="2000"
                    x-model="$store.modalForm.form.faction_required"
                />
                <x-form.input
                    name="level_required"
                    label="Level Required"
                    type="number"
                    min="0"
                    max="100"
                    x-model="$store.modalForm.form.level_required"
                />
                <x-form.input
                    name="probability"
                    label="Probability %"
                    tooltip="0 = Never, 100 = Always"
                    type="number"
                    step="1"
                    max="100"
                    x-model="$store.modalForm.form.probability"
                />
                <x-form.input
                    name="min_status"
                    label="Min Status"
                    type="number"
                    min="0"
                    max="255"
                    x-model="$store.modalForm.form.min_status"
                />
                <x-form.input
                    name="max_status"
                    label="Max Status"
                    type="number"
                    min="0"
                    max="255"
                    x-model="$store.modalForm.form.max_status"
                />
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Classes</h2>
            <div x-data="bitMaskPicker({ initial: $store.modalForm.form.classes_required ?? 0, fieldName: 'classes_required' })">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2">
                    <input type="hidden" :name="fieldName" :value="value">
                    @foreach(config('everquest.classes_bit', []) as $k => $label)
                        <div class="form-control">
                            <label class="label cursor-pointer gap-2">
                                <input
                                    type="checkbox"
                                    value="{{ $k }}"
                                    id="classes_{{ $k }}"
                                    x-model.number="checked"
                                    class=""
                                />
                                <span class="label-text">{{ $label }}</span>
                            </label>
                        </div>
                    @endforeach
                    <div class="form-control">
                        <label class="label cursor-pointer gap-2">
                            <input
                                type="checkbox"
                                data-all
                                x-model="allChecked"
                                @change="toggleAll()"
                                class=""
                            />
                            <span class="label-text font-semibold text-accent">All / None</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Bucket</h2>
            <div class="grid grid-cols-3 gap-4">
                <x-form.input
                    name="bucket_name"
                    label="Name"
                    x-model="$store.modalForm.form.bucket_name"
                />
                <x-form.input
                    name="bucket_value"
                    label="Value"
                    x-model="$store.modalForm.form.bucket_value"
                />
                <x-form.select
                    name="bucket_comparison"
                    label="Comparison"
                    :options="[
                        0 => '==',
                        1 => '!=',
                        2 => '>=',
                        3 => '<=',
                        4 => '>',
                        5 => '<',
                        6 => 'is any of',
                        7 => 'is not any of',
                        8 => 'is between',
                        9 => 'is not between',
                    ]"
                    keyInOption=true
                    x-model="$store.modalForm.form.bucket_comparison"
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
