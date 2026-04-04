<div class="space-y-6" x-data="formTracker">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-6 gap-4">
                <x-form.input
                    name="name"
                    label="Recipe Name"
                    x-model="$store.modalForm.form.name"
                    maxlength="64"
                    required
                    wrapper-class="col-span-3"
                />
                <x-form.select
                    name="tradeskill"
                    label="Tradeskill"
                    :options="$tradeskills->toArray()"
                    x-model="$store.modalForm.form.tradeskill"
                />
                <x-form.input
                    name="skillneeded"
                    label="Skill Needed"
                    type="number"
                    min="0"
                    x-model="$store.modalForm.form.skillneeded"
                />
                <x-form.input
                    name="trivial"
                    label="Trivial"
                    type="number"
                    min="0"
                    x-model="$store.modalForm.form.trivial"
                />
                <x-form.select
                    name="l_method"
                    label="Learned By"
                    :options="[0 => 'Not Learned', 1 => 'Quest', 2 => 'Experiment']"
                    x-model="$store.modalForm.form.flags.l_method"
                    wrapper-class="col-span-1"
                />

                <x-form.select
                    name="l_message"
                    label="Client Message"
                    :options="[0 => 'Yes', 16 => 'No']"
                    x-model="$store.modalForm.form.flags.l_message"
                    wrapper-class="col-span-1"
                />

                <x-form.select
                    name="l_search"
                    label="Searchable"
                    :options="[0 => 'Yes', 32 => 'No']"
                    x-model="$store.modalForm.form.flags.l_search"
                    wrapper-class="col-span-1"
                />
                <div
                    x-data="ajaxSelect({
                        searchUrl: '/items/search',
                        useModal: false,
                        prefillValue: $store.modalForm.form.learned_by_item_id
                            ? {
                                id: $store.modalForm.form.learned_by_item_id,
                                name: $store.modalForm.form.learned_by_item_name
                            }
                            : null,
                        allowNone: true,
                        noneId: 0,
                    })"
                    x-init="init()"
                    class="col-span-3"
                >
                    <label class="label">Learned By Item</label>
                    <select
                        x-ref="select"
                        name="learned_by_item_id"
                        class="w-full"
                    ></select>
                </div>
                <x-form.textarea
                    name="notes"
                    label=""
                    x-model="$store.modalForm.form.notes"
                    placeholder="Notes"
                    wrapper-class="col-span-6"
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
                    x-model="$store.modalForm.form.min_expansion"
                />
                <x-form.select
                    name="max_expansion"
                    label="Max Expansion"
                    :options="[-1 => 'Any'] + config('everquest.expansions')"
                    x-model="$store.modalForm.form.max_expansion"
                />
                <x-form.content-flag-select
                    name="content_flags"
                    label="Content Flags"
                    x-model="$store.modalForm.form.content_flags"
                />
                <x-form.content-flag-select
                    name="content_flags_disabled"
                    label="Content Flags Disabled"
                    x-model="$store.modalForm.form.content_flags_disabled"
                />
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Options</h2>
            <div class="flex flex-wrap items-center gap-4">
                <x-form.checkbox
                    name="enabled"
                    label="Enabled"
                    x-model="$store.modalForm.form.enabled"
                />
                <x-form.checkbox
                    name="nofail"
                    label="No Fail"
                    x-model="$store.modalForm.form.nofail"
                />
                <x-form.checkbox
                    name="quest"
                    label="Quest Controlled"
                    x-model="$store.modalForm.form.quest"
                />
                <x-form.checkbox
                    name="replace_container"
                    label="Replace Combine Container"
                    x-model="$store.modalForm.form.replace_container"
                />
            </div>
        </div>
    </div>
</div>
