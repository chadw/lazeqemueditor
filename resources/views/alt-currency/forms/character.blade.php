<div class="card bg-base-200 card-sm shadow-sm mb-45">
    <div class="card-body">
        <h2 class="card-title">
            Character:
            <span x-text="$store.modalForm.form.character?.name ?? ''"></span>
        </h2>
        <div class="grid grid-cols-3 gap-4">
            <div
                x-data="ajaxSelect({
                    searchUrl: '/characters/search',
                    prefillValue: () => $store.modalForm.form.character ?? null,
                })"
                x-init="init()"
            >
                <label class="label">Character</label>
                <select
                    x-ref="select"
                    name="char_id"
                    class="w-full"
                ></select>
            </div>
            <x-form.select
                name="currency_id"
                label="Currency"
                tooltip=""
                :options="['0' => 'None'] + $altCurrency->pluck('item.Name', 'id')->toArray()"
                x-model="$store.modalForm.form.currency_id"
            />
            <x-form.input
                name="amount"
                label="Amount"
                x-model="$store.modalForm.form.amount"
            />
        </div>
    </div>
</div>
