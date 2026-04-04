<div class="card bg-base-200 card-sm shadow-sm mb-4">
    <div class="card-body">
        <div class="grid grid-cols-7 gap-4">
            <x-form.select
                name="zone_id"
                label="Zone"
                :options="$zones"
                keyInOption=true
                x-model="$store.modalForm.form.zone_id"
                wrapper-class="col-span-3"
            />
            <x-form.input
                name="x"
                label="X"
                type="number"
                x-model="$store.modalForm.form.x"
            />
            <x-form.input
                name="y"
                label="Y"
                type="number"
                x-model="$store.modalForm.form.y"
            />
            <x-form.input
                name="z"
                label="Z"
                type="number"
                x-model="$store.modalForm.form.z"
            />
            <x-form.input
                name="heading"
                label="Heading"
                type="number"
                x-model="$store.modalForm.form.heading"
            />
        </div>
    </div>
</div>
