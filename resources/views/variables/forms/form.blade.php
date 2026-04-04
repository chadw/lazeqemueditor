<div class="space-y-6" x-data="formTracker">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h3 class="card-title">
                Name: <span class="" x-text="$store.modalForm.form.varname"></span>
            </h3>
            <div class="grid grid-cols-1 gap-4">
                <x-form.textarea
                    name="value"
                    label="Value"
                    x-model="$store.modalForm.form.value"
                    rows="4"
                />
                <x-form.textarea
                    name="information"
                    label="Description"
                    x-model="$store.modalForm.form.information"
                    rows="3"
                />
            </div>
        </div>
    </div>
</div>
