<div class="space-y-6">
    <div class="card bg-base-200 card-sm shadow-sm mb-45">
        <div class="card-body">
            <div class="grid grid-cols-4 gap-4">
                <div
                    x-data='ajaxSelect({
                        searchUrl: "/factions/search",
                        useModal: true,
                        prefillValue: () => $store.modalForm.form.faction ?? null,
                        keyInOption: true,
                        required: true,
                    })'
                    x-init="init()"
                    class="col-span-3"
                >
                    <label class="label">Faction</label>
                    <select
                        x-ref="select"
                        name="faction_id"
                        class="w-full validator invalid:select-error"
                        required
                    ></select>
                </div>
                <x-form.input
                    name="current_value"
                    label="Value"
                    type="number"
                    min="-2000"
                    max="2000"
                    x-model="$store.modalForm.form.current_value"
                />
            </div>
        </div>
    </div>
</div>
