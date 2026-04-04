<div class="space-y-6" x-data="formTracker" x-init="$store.containerTemplateForm.init();">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h3 class="card-title">Template Info</h3>
            <div class="grid grid-cols-2 gap-4">
                <x-form.input
                    name="name"
                    label="Template Name"
                    x-model="$store.modalForm.form.name"
                />
                <x-form.select
                    name="skill"
                    label="Tradeskill"
                    :options="config('everquest.skills.tradeskill')"
                    x-model="$store.modalForm.form.skill"
                />
            </div>
        </div>
    </div>

    <div class="card bg-base-200 card-sm shadow-sm mb-45">
        <div class="card-body space-y-4">
            <div class="flex justify-between items-center">
                <h3 class="card-title">Container Items</h3>
                <button type="button"
                        class="btn btn-sm btn-soft btn-success"
                        @click="$store.containerTemplateForm.addItem()">
                    <x-ui.icon name="add" /> Add Item
                </button>
            </div>
            <template x-for="(item, index) in $store.modalForm.form.items" :key="index">
                <div class="flex items-end gap-3">
                    <div class="flex-1"
                        x-data="ajaxSelect({
                            searchUrl: '/items/search',
                            prefillValue: () => item.resolved_item ?? null,
                            allowNone: false,
                            seedOptions: {{ Js::from(config('everquest.tradeskill_containers')) }},
                        })"
                        x-init="init()"
                    >
                        <select
                            x-ref="select"
                            :name="`items[${index}][item_id]`"
                            class="w-full"
                        ></select>
                    </div>
                    <div>
                        <button
                            type="button"
                            class="btn btn-soft btn-error"
                            @click="$store.containerTemplateForm.removeItem(index)"
                        >
                            <x-ui.icon name="delete" />
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
