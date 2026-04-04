<div class="space-y-6" x-data="formTracker">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-6 gap-4 mb-2">
                <x-form.input
                    name="name"
                    label="Name"
                    wrapper-class="col-span-5"
                    required
                    x-model="$store.modalForm.form.name"
                />
                <x-form.input
                    name="base"
                    label="Base"
                    type="number"
                    min="0"
                    wrapper-class="col-span-1"
                    x-model.number="$store.modalForm.form.base"
                />
            </div>
            <div class="grid grid-cols-5 gap-4 mb-2">
                <x-form.input
                    name="faction_base_data[min]"
                    label="Min Faction"
                    type="number"
                    min="-2000"
                    max="2000"
                    x-model="$store.modalForm.form.faction_base_data.min"
                />
                <x-form.input
                    name="faction_base_data[max]"
                    label="Max Faction"
                    type="number"
                    min="-2000"
                    max="2000"
                    x-model="$store.modalForm.form.faction_base_data.max"
                />
                <x-form.input
                    name="faction_base_data[unk_hero1]"
                    label="Hero 1"
                    type="number"
                    min="0"
                    x-model="$store.modalForm.form.faction_base_data.unk_hero1"
                />
                <x-form.input
                    name="faction_base_data[unk_hero2]"
                    label="Hero 2"
                    type="number"
                    min="0"
                    x-model="$store.modalForm.form.faction_base_data.unk_hero2"
                />
                <x-form.input
                    name="faction_base_data[unk_hero3]"
                    label="Hero 3"
                    type="number"
                    min="0"
                    x-model="$store.modalForm.form.faction_base_data.unk_hero3"
                />
            </div>
        </div>
    </div>
</div>
