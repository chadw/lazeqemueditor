<div class="space-y-6">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-1 gap-4">
                <x-form.input
                    name="flag_name"
                    label="Flag Name"
                    maxlength="75"
                    x-model="$store.modalForm.form.flag_name"
                />
                <x-form.checkbox
                    name="enabled"
                    label="Enabled"
                    tooltip=""
                    x-model="$store.modalForm.form.enabled"
                />
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Notes</h2>
            <div class="grid grid-cols-1 gap-4">
                <x-form.textarea
                    name="notes"
                    label=""
                    x-model="$store.modalForm.form.notes"
                />
            </div>
        </div>
    </div>
</div>
