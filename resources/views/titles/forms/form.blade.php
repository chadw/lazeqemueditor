<div class="space-y-6" x-data="formTracker">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Title</h2>
            <div class="grid grid-cols-2 gap-4">
                <x-form.input
                    name="prefix"
                    label="Prefix"
                    x-model="$store.modalForm.form.prefix"
                    maxlength="31"
                />
                <x-form.input
                    name="suffix"
                    label="Suffix"
                    x-model="$store.modalForm.form.suffix"
                    maxlength="31"
                />
            </div>
        </div>
    </div>

    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-2 gap-4">
                <div
                    x-data="ajaxSelect({
                        searchUrl: '/characters/search',
                        prefillValue: () => $store.modalForm.form.character ?? null,
                        allowNone: true,
                    })"
                    x-init="init()"
                >
                    <label class="label">Character</label>
                    <select
                        x-ref="select"
                        name="char_id"
                        class="w-full"
                    ></select>
                </div>
                <div
                    x-data="ajaxSelect({
                        searchUrl: '/items/search',
                        prefillValue: () => $store.modalForm.form.item ?? null,
                        allowNone: true,
                    })"
                    x-init="init()"
                >
                    <label class="label">Item</label>
                    <select
                        x-ref="select"
                        name="item_id"
                        class="w-full"
                    ></select>
                </div>
                <x-form.select
                    name="class"
                    label="Class"
                    tooltip=""
                    :options="['-1' => 'N/A'] + config('everquest.classes')"
                    x-model="$store.modalForm.form.class"
                />
                <x-form.select
                    name="gender"
                    label="Gender"
                    tooltip=""
                    :options="[
                        '-1' => 'N/A',
                        0 => 'Male',
                        1 => 'Female',
                        2 => 'Neuter',
                    ]"
                    x-model="$store.modalForm.form.gender"
                />
                {{-- overwrite -1 (suspended) with none --}}
                <x-form.select
                    name="status"
                    label="Status"
                    tooltip=""
                    :options="['-1' => 'None'] + config('everquest.account_status')"
                    x-model="$store.modalForm.form.status"
                />
                <x-form.input
                    name="title_set"
                    label="Title Set"
                    x-model="$store.modalForm.form.title_set"
                />
                <div class="join col-span-2">
                    <x-form.select
                        name="skill_id"
                        label="Skill"
                        tooltip=""
                        :options="['-1' => 'None'] + config('everquest.db_skills')"
                        x-model="$store.modalForm.form.skill_id"
                        class="join-item"
                    />
                    <x-form.input
                        name="min_skill_value"
                        label="Min Skill"
                        x-model="$store.modalForm.form.min_skill_value"
                        class="join-item"
                    />
                    <x-form.input
                        name="max_skill_value"
                        label="Max Skill"
                        x-model="$store.modalForm.form.max_skill_value"
                        class="join-item"
                    />
                </div>
            </div>
        </div>
    </div>
</div>
