<div class="card bg-base-200 card-sm shadow-sm mb-4">
    <div class="card-body">
        <div class="grid grid-cols-4 gap-4">
            <input type="hidden" name="rank_id" :value="$store.modalForm.form.rank_id" />
            <input type="hidden" name="slot" :value="$store.modalForm.form.slot" />
            <x-form.select
                name="effect_id"
                label="Effect"
                :options="config('everquest.spell_effects')"
                keyInOption="true"
                x-model="$store.modalForm.form.effect_id"
                wrapper-class="col-span-2"
            />
            <x-form.input
                name="base1"
                label="Base 1"
                x-model="$store.modalForm.form.base1"
            />
            <x-form.input
                name="base2"
                label="Base 2"
                x-model="$store.modalForm.form.base2"
            />
        </div>
    </div>
</div>
