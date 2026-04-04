<div class="space-y-6">
    <div class="card bg-base-200 card-sm shadow-sm mb-45">
        <div class="card-body">
            <h2 class="card-title">
                NPC: <span x-text="$store.modalForm.form.npc?.name ?? ''"></span>
            </h2>
            <div class="grid grid-cols-2 gap-4">
                <div
                    x-data="ajaxSelect({
                        searchUrl: '/npcs/search',
                        prefillValue: () => $store.modalForm.form.npc ?? null,
                    })"
                    x-init="init()"
                >
                    <label class="label">Npc</label>
                    <select
                        x-ref="select"
                        name="npc_id"
                        class="w-full"
                    ></select>
                </div>
                <x-form.select
                    name="alt_currency_id"
                    label="Currency"
                    tooltip=""
                    :options="['0' => 'None'] + $altCurrency->pluck('item.Name', 'id')->toArray()"
                    x-model="$store.modalForm.form.alt_currency_id"
                />
            </div>
        </div>
    </div>
</div>
