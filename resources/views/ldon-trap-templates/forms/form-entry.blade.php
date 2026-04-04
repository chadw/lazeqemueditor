<div class="space-y-6">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-3 gap-4">
                <input
                    type="hidden"
                    name="id"
                    :value="$store.modalForm.form.id"
                />
                <x-form.input
                    name="trap_id"
                    label="Trap ID"
                    x-model="$store.modalForm.form.trap_id"
                />
            </div>
        </div>
    </div>
</div>
