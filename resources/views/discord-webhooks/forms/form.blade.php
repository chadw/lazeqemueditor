<div class="space-y-6">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-4 gap-4">
                <x-form.input
                    name="webhook_name"
                    label="Webhook Name"
                    maxlength="100"
                    required
                    x-model="$store.modalForm.form.webhook_name"
                />
                <x-form.input
                    name="webhook_url"
                    label="Webhook URL"
                    maxlength="255"
                    required
                    x-model="$store.modalForm.form.webhook_url"
                    wrapper-class="col-span-3"
                />
            </div>
        </div>
    </div>
</div>
