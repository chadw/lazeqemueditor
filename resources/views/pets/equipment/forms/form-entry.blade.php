<div class="space-y-6" x-data="formTracker">
    <div class="card bg-base-200 card-sm shadow-sm mb-45">
        <div class="card-body">
            <div class="grid grid-cols-2 gap-4">
                <input
                    type="hidden"
                    name="set_id"
                    :value="$store.modalForm.form.set_id"
                />
                <x-form.select
                    name="slot"
                    label="Slot"
                    :options="config('everquest.slots_inv')"
                    x-model="$store.modalForm.form.slot"
                />
                <div
                    x-data="ajaxSelect({
                        searchUrl: '/items/search',
                        prefillValue: () => $store.modalForm.form.item ?? null,
                    })"
                    x-init="init()"
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
