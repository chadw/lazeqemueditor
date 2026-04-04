<div class="space-y-6" x-data="formTracker">
    <div class="card bg-base-200 card-sm shadow-sm mb-45">
        <div class="card-body">
            <div class="grid grid-cols-4 gap-4">
                <x-form.input
                    name="level"
                    label="Level"
                    type="number"
                    min="1"
                    max="255"
                    x-model="$store.modalForm.form.level"
                />
                <x-form.input
                    name="cost"
                    label="Cost"
                    type="number"
                    min="0"
                    x-model="$store.modalForm.form.cost"
                />
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
            </div>
        </div>
    </div>
</div>
