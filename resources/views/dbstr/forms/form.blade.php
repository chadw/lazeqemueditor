<div class="space-y-6" x-data="formTracker">
    <div class="card bg-base-200 card-sm shadow-sm mb-5">
        <div class="card-body">
            <div class="grid grid-cols-1 gap-4">
                <input type="hidden" name="type" x-model="$store.modalForm.form.type">
                <input type="hidden" name="id" x-model="$store.modalForm.form.id">
                <x-form.textarea
                    name="value"
                    label="Value"
                    rows="8"
                    x-model="$store.modalForm.form.value"
                />
            </div>
        </div>
    </div>
</div>
