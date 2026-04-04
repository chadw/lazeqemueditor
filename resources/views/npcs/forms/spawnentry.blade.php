<div class="space-y-6" x-data="formTracker">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-8 gap-4">
                <div
                    x-data="ajaxSelect({
                        searchUrl: '/npcs/search',
                        prefillValue: () => $store.modalForm.form.entry ?? null,
                        allowNone: false,
                    })"
                    x-init="init()"
                    class="col-span-4"
                >
                    <label class="label">Npc</label>
                    <select
                        x-ref="select"
                        name="npcID"
                        class="w-full"
                        required
                    ></select>
                </div>

                <x-form.input
                    name="chance"
                    label="Chance"
                    type="number"
                    min="0"
                    max="100"
                    x-model="$store.modalForm.form.spawn.chance"
                />
                <x-form.input
                    name="condition_value_filter"
                    label="Condition Value Filter"
                    type="number"
                    min="0"
                    x-model="$store.modalForm.form.spawn.condition_value_filter"
                />
                <x-form.input
                    name="min_time"
                    label="Min Time"
                    type="number"
                    min="0"
                    x-model="$store.modalForm.form.spawn.min_time"
                />
                <x-form.input
                    name="max_time"
                    label="Max Time"
                    type="number"
                    min="0"
                    x-model="$store.modalForm.form.spawn.max_time"
                />
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <x-form.select
                    name="min_expansion"
                    label="Min Expansion"
                    :options="[-1 => 'Any'] + config('everquest.expansions')"
                    x-model="$store.modalForm.form.spawn.min_expansion"
                />
                <x-form.select
                    name="max_expansion"
                    label="Max Expansion"
                    :options="[-1 => 'Any'] + config('everquest.expansions')"
                    x-model="$store.modalForm.form.spawn.max_expansion"
                />
                <x-form.content-flag-select
                    name="content_flags"
                    label="Content Flags"
                    x-model="$store.modalForm.form.spawn.content_flags"
                />
                <x-form.content-flag-select
                    name="content_flags_disabled"
                    label="Content Flags Disabled"
                    x-model="$store.modalForm.form.spawn.content_flags_disabled"
                />
            </div>
        </div>
    </div>
</div>
