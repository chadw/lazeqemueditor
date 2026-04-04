@php
    $levels = collect(range(1, 100))->mapWithKeys(fn ($v) => [$v => $v])->toArray();
@endphp
<div class="space-y-6" x-data="formTracker">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-2 gap-4 mb-4">
                <x-form.select
                    name="level"
                    label="Level"
                    :options="$levels"
                    x-model="$store.modalForm.form.level"
                />
                <x-form.select
                    name="class"
                    label="Class"
                    :options="config('everquest.classes')"
                    x-model="$store.modalForm.form.class"
                />
            </div>
            <div class="grid grid-cols-4 sm:grid-cols-8 gap-4 mb-4">
                <x-form.input
                    name="hp"
                    label="HP"
                    type="number"
                    min="0"
                    x-model="$store.modalForm.form.hp"
                />
                <x-form.input
                    name="mana"
                    label="Mana"
                    type="number"
                    min="0"
                    x-model="$store.modalForm.form.mana"
                />
                <x-form.input
                    name="end"
                    label="Endurance"
                    type="number"
                    min="0"
                    x-model="$store.modalForm.form.end"
                />
                <x-form.input
                    name="hp_regen"
                    label="HP Regen"
                    type="number"
                    min="0"
                    x-model="$store.modalForm.form.hp_regen"
                />
                <x-form.input
                    name="end_regen"
                    label="Endurance Regen"
                    type="number"
                    min="0"
                    x-model="$store.modalForm.form.end_regen"
                />
                <x-form.input
                    name="hp_fac"
                    label="HP Faction"
                    type="number"
                    min="0"
                    step="any"
                    x-model="$store.modalForm.form.hp_fac"
                />
                <x-form.input
                    name="mana_fac"
                    label="Mana Faction"
                    type="number"
                    min="0"
                    step="any"
                    x-model="$store.modalForm.form.mana_fac"
                />
                <x-form.input
                    name="end_fac"
                    label="Endurance Faction"
                    type="number"
                    min="0"
                    step="any"
                    x-model="$store.modalForm.form.end_fac"
                />
            </div>
        </div>
    </div>
</div>
