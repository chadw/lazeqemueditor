<div class="space-y-6">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-2 gap-4">
                <input type="hidden" name="npc_id" x-model="$store.modalForm.form.npc_id" />
                <x-form.input
                    name="name"
                    label="Name"
                    x-model="$store.modalForm.form.name"
                    required
                />
                <div
                    x-data="ajaxSelect({
                        searchUrl: '/factions/search',
                        prefillValue: () => null,
                        allowNone: true,
                    })"
                    x-init="init()"
                >
                    <label class="label">Primary Faction</label>
                    <select
                        x-ref="select"
                        name="primaryfaction"
                        class="w-full"
                        required
                    ></select>
                </div>
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm mb-20">
        <div class="card-body">
            <h2 class="card-title">Options</h2>
            <div class="flex flex-wrap items-center gap-4">
                <x-form.checkbox
                    name="ignore_primary_assist"
                    label="Ignore Primary Assist"
                    x-model="$store.modalForm.form.ignore_primary_assist"
                />
            </div>
        </div>
    </div>
</div>
