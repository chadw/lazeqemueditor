<div class="space-y-6" x-data="formTracker">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-6 gap-4">
                <x-form.input
                    name="name"
                    label="Name"
                    x-model="$store.modalForm.form.name"
                    wrapper-class="col-span-3"
                    required
                />
                <x-form.input
                    name="owner"
                    label="Owner"
                    x-model="$store.modalForm.form.owner"
                />
                <x-form.input
                    name="password"
                    label="Password"
                    x-model="$store.modalForm.form.password"
                />
                <x-form.select
                    name="minstatus"
                    label="Min Status"
                    :options="config('everquest.account_status')"
                    keyInOption="true"
                    x-model="$store.modalForm.form.minstatus"
                />
            </div>
        </div>
    </div>
</div>
