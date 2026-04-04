<div class="card bg-base-200 card-sm shadow-sm">
    <div class="card-body">
        <div class="grid grid-cols-4 gap-4 mb-2">
            <x-form.input
                name="lootdrop[name]"
                label="Name"
                x-model="$store.modalForm.form.lootdrop.name"
                wrapper-class="col-span-4"
            />
            <x-form.input
                name="entry[mindrop]"
                label="Min Drops"
                type="number"
                x-model="$store.modalForm.form.entry.mindrop"
            />
            <x-form.input
                name="entry[droplimit]"
                label="Max Drops"
                type="number"
                x-model="$store.modalForm.form.entry.droplimit"
            />
            <x-form.input
                name="entry[multiplier]"
                label="Multiplier"
                type="number"
                min="1"
                max="100"
                x-model="$store.modalForm.form.entry.multiplier"
            />
            <x-form.input
                name="entry[probability]"
                label="Probability %"
                tooltip="0 = Never, 100 = Always"
                type="number"
                step="any"
                min="0"
                max="100"
                x-model="$store.modalForm.form.entry.probability"
            />
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <x-form.select
                name="lootdrop[min_expansion]"
                label="Min Expansion"
                :options="[-1 => 'Any'] + config('everquest.expansions')"
                x-model="$store.modalForm.form.lootdrop.min_expansion"
            />
            <x-form.select
                name="lootdrop[max_expansion]"
                label="Max Expansion"
                :options="[-1 => 'Any'] + config('everquest.expansions')"
                x-model="$store.modalForm.form.lootdrop.max_expansion"
            />
            <x-form.content-flag-select
                name="lootdrop[content_flags]"
                label="Content Flags"
                x-model="$store.modalForm.form.lootdrop.content_flags"
            />
            <x-form.content-flag-select
                name="lootdrop[content_flags_disabled]"
                label="Content Flags Disabled"
                x-model="$store.modalForm.form.lootdrop.content_flags_disabled"
            />
        </div>
    </div>
</div>
