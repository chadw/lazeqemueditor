<div class="card bg-base-200 card-sm shadow-sm mb-4">
    <div class="card-body">
        <div class="grid grid-cols-5 gap-4">
            <div
                x-data="ajaxSelect({
                    searchUrl: '/items/search',
                    prefillValue: () => $store.modalForm.form.item ?? null,
                })"
                x-init="init()"
                class="col-span-2"
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
                label="Quantity"
                type="number"
                x-model="$store.modalForm.form.item_charges"
            />
            <x-form.input
                name="inventory_slot"
                label="Slot"
                type="number"
                x-model="$store.modalForm.form.inventory_slot"
            />
            <x-form.input
                name="status"
                label="Status"
                type="number"
                x-model="$store.modalForm.form.status"
            />
        </div>
    </div>
</div>

<div class="card bg-base-200 card-sm shadow-sm mb-4">
    <div class="card-body">
        <div class="grid grid-cols-2 gap-4">
            <x-form.multi-select
                name="class_list"
                label="Classes"
                :options="config('everquest.classes_abbr')"
                x-model="$store.modalForm.form._classes"
                placeholder="All Classes"
            />
            <x-form.multi-select
                name="race_list"
                label="Races"
                :options="config('everquest.races')"
                x-model="$store.modalForm.form._races"
                placeholder="All Races"
            />
            <x-form.multi-select
                name="deity_list"
                label="Deities"
                :options="config('everquest.deity')"
                x-model="$store.modalForm.form._deities"
                placeholder="All Deities"
            />
            <x-form.multi-select
                name="zone_id_list"
                label="Zones"
                :options="$zones"
                x-model="$store.modalForm.form._zones"
                placeholder="All Zones"
            />
        </div>
    </div>
</div>

<div class="card bg-base-200 card-sm shadow-sm mb-4">
    <div class="card-body">
        <h2 class="card-title">Augments</h2>
        <div class="grid grid-cols-2 gap-4">
            <div
                x-data="ajaxSelect({
                    searchUrl: '/items/search',
                    prefillPath: '',
                    allowNone: true,
                    noneId: 0,
                })"
                x-init="init()"
            >
                <label class="label">Augment 1</label>
                <select
                    x-ref="select"
                    name="augment_one"
                    class="w-full"
                ></select>
            </div>
            <div
                x-data="ajaxSelect({
                    searchUrl: '/items/search',
                    prefillPath: '',
                    allowNone: true,
                    noneId: 0,
                })"
                x-init="init()"
            >
                <label class="label">Augment 2</label>
                <select
                    x-ref="select"
                    name="augment_two"
                    class="w-full"
                ></select>
            </div>
            <div
                x-data="ajaxSelect({
                    searchUrl: '/items/search',
                    prefillPath: '',
                    allowNone: true,
                    noneId: 0,
                })"
                x-init="init()"
            >
                <label class="label">Augment 3</label>
                <select
                    x-ref="select"
                    name="augment_three"
                    class="w-full"
                ></select>
            </div>
            <div
                x-data="ajaxSelect({
                    searchUrl: '/items/search',
                    prefillPath: '',
                    allowNone: true,
                    noneId: 0,
                })"
                x-init="init()"
            >
                <label class="label">Augment 4</label>
                <select
                    x-ref="select"
                    name="augment_four"
                    class="w-full"
                ></select>
            </div>
            <div
                x-data="ajaxSelect({
                    searchUrl: '/items/search',
                    prefillPath: '',
                    allowNone: true,
                    noneId: 0,
                })"
                x-init="init()"
            >
                <label class="label">Augment 5</label>
                <select
                    x-ref="select"
                    name="augment_five"
                    class="w-full"
                ></select>
            </div>
            <div
                x-data="ajaxSelect({
                    searchUrl: '/items/search',
                    prefillPath: '',
                    allowNone: true,
                    noneId: 0,
                })"
                x-init="init()"
            >
                <label class="label">Augment 6</label>
                <select
                    x-ref="select"
                    name="augment_six"
                    class="w-full"
                ></select>
            </div>
        </div>
    </div>
</div>

<div class="card bg-base-200 card-sm shadow-sm mb-4">
    <div class="card-body">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <x-form.range
                name="min_expansion"
                label="Min Expansion"
                min="-1"
                max="{{ array_key_last(config('everquest.expansions')) }}"
                x-model="$store.modalForm.form.min_expansion"
            />
            <x-form.range
                name="max_expansion"
                label="Max Expansion"
                min="-1"
                max="{{ array_key_last(config('everquest.expansions')) }}"
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
