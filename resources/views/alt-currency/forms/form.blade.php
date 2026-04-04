<div class="space-y-6">
    <div class="card bg-base-200 card-sm shadow-sm mb-45">
        <div class="card-body">
            <div class="grid grid-cols-1 gap-4">
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
