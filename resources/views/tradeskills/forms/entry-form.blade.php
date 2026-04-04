<div x-data="formTracker">
    <input type="hidden" name="recipe_id" x-model="$store.modalForm.form.recipe_id" />
    <div class="card bg-base-200 card-sm shadow-sm mb-4">
        <div class="card-body">
            <div class="grid grid-cols-12 gap-4 items-center">
                <div class="col-span-3" x-cloak :class="$store.modalForm.form.iscontainer || $store.modalForm.resourceName === 'Container' ? '' : 'hidden'">
                    <x-form.select
                        name="template_container_id"
                        label="Container Template"
                        :options="['' => 'None'] + $containerTemplates->pluck('name', 'id')->toArray()"
                        x-model="$store.modalForm.form.template_container_id"
                    />
                </div>

                <div class="col-span-1 flex justify-center items-center" x-cloak :class="$store.modalForm.form.iscontainer || $store.modalForm.resourceName === 'Container' ? '' : 'hidden'">
                    <span class="divider divider-vertical">OR</span>
                </div>

                <div
                    x-data="ajaxSelect({
                        searchUrl: '/items/search',
                        prefillValue: () => {
                            const form = $store.modalForm.form;

                            if (form.item) {
                                return form.item;
                            }

                            if (form.container_id) {
                                return {
                                    id: form.container_id,
                                    name: form.container_name,
                                    icon: form.container_icon
                                };
                            }

                            return null;
                        },
                        seedOptions: {{ Js::from(config('everquest.tradeskill_containers')) }},
                    })"
                    x-init="init()"
                    :class="$store.modalForm.form.iscontainer || $store.modalForm.resourceName === 'Container' ? 'col-span-8' : 'col-span-12'"
                >
                    <label class="label">Item</label>
                    <select
                        x-ref="select"
                        name="item_id"
                        class="w-full"
                    ></select>
                </div>
            </div>

            <template x-if="!$store.modalForm.form.iscontainer && $store.modalForm.resourceName === 'Component'">
                <div class="grid grid-cols-4 gap-4 mt-2">
                    <x-form.input
                        name="componentcount"
                        label="Component Count"
                        type="number"
                        min="1"
                        x-model="$store.modalForm.form.componentcount"
                        x-bind:readonly="$store.modalForm.form.iscontainer"
                    />
                    <x-form.input
                        name="failcount"
                        label="Qty Returned on Fail"
                        type="number"
                        min="0"
                        x-model="$store.modalForm.form.failcount"
                        x-bind:readonly="$store.modalForm.form.iscontainer"
                    />
                    <x-form.input
                        name="salvagecount"
                        label="Qty Returned on Salvage"
                        type="number"
                        min="0"
                        x-model="$store.modalForm.form.salvagecount"
                        x-bind:readonly="$store.modalForm.form.iscontainer"
                    />
                </div>
            </template>
            <template x-if="!$store.modalForm.form.iscontainer && $store.modalForm.resourceName === 'Result'">
                <div class="grid grid-cols-4 gap-4 mt-2">
                    <x-form.input
                        name="successcount"
                        label="Qty Produced on Success"
                        type="number"
                        min="1"
                        x-model="$store.modalForm.form.successcount"
                        x-bind:readonly="$store.modalForm.form.iscontainer"
                    />
                </div>
            </template>
        </div>
    </div>

    <template x-if="$store.modalForm.form.iscontainer || $store.modalForm.resourceName === 'Container'">
        <div class="card bg-base-200 card-sm shadow-sm mb-25">
            <div class="card-body">
                <div class="flex flex-wrap items-center gap-4">
                    <x-form.checkbox
                        name="iscontainer"
                        label="Is Container"
                        x-model="$store.modalForm.form.iscontainer"
                    />
                </div>
            </div>
        </div>
    </template>
</div>
