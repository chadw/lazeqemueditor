<dialog id="evolving-picker-modal" class="modal"
    x-data
    x-effect="$store.evolvingPicker.isOpen ? $el.showModal() : $el.close()"
    @close="$store.evolvingPicker.close()"
>
    <div class="modal-box max-w-4xl flex flex-col max-h-[85vh]">
        <div class="flex items-center justify-between pb-6 border-b border-base-content/10">
            <h2 class="text-lg font-semibold">Pick Evolving Group</h2>
            <button class="btn btn-soft btn-circle" @click="$store.evolvingPicker.close()">
                <x-ui.icon name="close" />
            </button>
        </div>

        <div class="my-3 text-sm" x-show="$store.evolvingPicker && $store.evolvingPicker.previewValue" x-cloak>
            <span class="opacity-60">Current:</span>
            <span class="text-accent" x-text="$store.evolvingPicker.previewValue"></span>
        </div>

        <div class="flex-1 overflow-y-auto bg-base-100 border border-base-content/10 rounded">
            <table class="table table-sm table-zebra w-full table-pin-rows">
                <thead class="text-xs uppercase bg-neutral">
                    <tr>
                        <th class="w-[10%]">Evo ID</th>
                        <th>Items</th>
                        <th class="w-[10%]"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="$store.evolvingPicker && $store.evolvingPicker.loading">
                        <tr>
                            <td colspan="3" class="text-center py-8">
                                <span class="loading loading-spinner loading-md"></span>
                            </td>
                        </tr>
                    </template>
                    <template x-if="$store.evolvingPicker && !$store.evolvingPicker.loading && $store.evolvingPicker.groups && $store.evolvingPicker.groups.length === 0">
                        <tr>
                            <td colspan="3" class="text-center py-8 opacity-50">No evolving groups found.</td>
                        </tr>
                    </template>
                    <template x-for="group in ($store.evolvingPicker && $store.evolvingPicker.groups) || []" :key="group.evoId">
                        <tr>
                            <td class="text-xs" x-text="group.evoId"></td>
                            <td class="text-sm">
                                <template x-for="row in group.items" :key="`${group.evoId}-${row.item_id}`">
                                    <div class="inline-flex items-center gap-2 mr-2 mb-1">
                                        <a :href="`/items/${row.item_id}`"
                                            @mouseenter="$store.tooltip.loadTooltip(`/items/popup/${row.item_id}`, $el, $event)"
                                            @mouseleave="$store.tooltip.hideTooltip()"
                                            class="text-base link-info link-hover flex items-center gap-1"
                                            :title="row.item ? row.item.Name : ('#' + row.item_id)">

                                            <template x-if="row.item && row.item.icon">
                                                <span class="icon-wrap" aria-hidden="true">
                                                    <span :class="`item-icon item-${row.item.icon} item-icon-sm`"></span>
                                                </span>
                                            </template>

                                            <span class="whitespace-nowrap text-xs" x-text="row.item ? row.item.Name : ('#' + row.item_id)"></span>
                                        </a>
                                    </div>
                                </template>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-soft btn-accent" @click="$store.evolvingPicker.select(group.evoId)">Use</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <div class="modal-action">
            <button type="button" class="btn" @click="$store.evolvingPicker.close()">Close</button>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop"><button>close</button></form>
</dialog>
