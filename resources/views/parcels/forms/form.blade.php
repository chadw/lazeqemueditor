<div class="card bg-base-200 card-sm shadow-sm mb-4">
    <div class="card-body">
        <div class="grid grid-cols-2 gap-4">
            <div
                x-data="ajaxSelect({
                    searchUrl: '/characters/search',
                    prefillValue: () => $store.modalForm.form.character ?? null,
                    allowNone: false,
                    required: true,
                })"
                x-init="init()"
            >
                <label class="label">Character</label>
                <select
                    x-ref="select"
                    name="char_id"
                    class="w-full validator invalid:select-error"
                    required
                ></select>
            </div>
            <div
                x-data="ajaxSelect({
                    searchUrl: '/items/search',
                    prefillValue: () => $store.modalForm.form.item ?? null,
                    allowNone: false,
                    required: true,
                })"
                x-init="init()"
            >
                <label class="label">Item</label>
                <select
                    x-ref="select"
                    name="item_id"
                    class="w-full validator invalid:select-error"
                    required
                ></select>
            </div>
            <x-form.input
                name="quantity"
                label="Quantity"
                type="number"
                min="1"
                required
                x-model="$store.modalForm.form.quantity"
            />
            <x-form.input
                name="from_name"
                label="From Name"
                x-model="$store.modalForm.form.from_name"
            />
            <x-form.input
                name="evolve_amount"
                label="Evolve Amount"
                type="number"
                min="0"
                required
                x-model="$store.modalForm.form.evolve_amount"
            />
        </div>
    </div>
</div>

<div class="card bg-base-200 card-sm shadow-sm mb-4">
    <div class="card-body">
        <h2 class="card-title">Aug Slots</h2>
        <div class="grid grid-cols-3 gap-4">
            @for($i = 1; $i <= 6; $i++)
                <div
                    x-data="ajaxSelect({
                        searchUrl: '/items/search',
                        prefillValue: () => $store.modalForm.form.aug{{ $i }} ?? null,
                        allowNone: false,
                        required: true,
                    })"
                    x-init="init()"
                >
                    <label class="label">Aug Slot {{ $i }}</label>
                    <select
                        x-ref="select"
                        name="aug_slot_{{ $i }}"
                        class="w-full validator invalid:select-error"
                        required
                    ></select>
                </div>
            @endfor
        </div>
    </div>
</div>

<div class="card bg-base-200 card-sm shadow-sm">
    <div class="card-body">
        <div class="grid grid-cols-1 gap-4">
            <x-form.textarea
                name="note"
                label="Note"
                x-model="$store.modalForm.form.note"
            />
        </div>
    </div>
</div>
