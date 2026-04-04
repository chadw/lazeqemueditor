<div class="card bg-base-200 card-sm shadow-sm">
    <div class="card-body">
        <div class="grid grid-cols-4 gap-4">
            <x-form.input
                name="lootdrop[name]"
                label="Name"
                x-model="$store.modalForm.form.lootdrop.name"
                wrapper-class="col-span-4"
            />
        </div>
    </div>
</div>
