<dialog id="dbstr-picker-modal" class="modal"
    x-data
    x-effect="$store.dbstrPicker.isOpen ? $el.showModal() : $el.close()"
    @close="$store.dbstrPicker.close()"
>
        <div class="modal-box max-w-4xl flex flex-col max-h-[85vh]">
        <div class="flex items-center justify-between pb-6 border-b border-base-content/10">
            <h2 class="text-lg font-semibold">Search DBStr</h2>
            <button class="btn btn-soft btn-circle" :disabled="$store.dbstrPicker.saving"
                @click="if(!$store.dbstrPicker.saving) $store.dbstrPicker.close()">
                <x-ui.icon name="close" />
            </button>
        </div>
        <div class="my-3 text-sm" x-show="$store.dbstrPicker.previewValue" x-cloak>
            <span class="opacity-60">Current:</span>
            <span class="text-accent" x-text="$store.dbstrPicker.previewValue"></span>
        </div>
        <div class="flex items-center gap-3 mb-4">
            <input
                type="text"
                placeholder="Search by ID or text..."
                class="input flex-1 min-w-0"
                x-model="$store.dbstrPicker.query"
                @input.debounce.300ms="$store.dbstrPicker.search(1)"
                @keydown.enter.prevent="$store.dbstrPicker.search(1)"
            />
            <div class="flex items-center gap-2 shrink-0">
                <button type="button" class="btn btn-soft btn-success"
                    @click="$store.dbstrPicker.startCreate()"
                    x-show="!$store.dbstrPicker.creating">
                    <x-ui.icon name="add" /> New
                </button>
                <button type="button" class="btn btn-soft"
                    @click="$store.dbstrPicker.startEdit()"
                    x-show="$store.dbstrPicker.previewValue && !$store.dbstrPicker.editing">
                    <x-ui.icon name="edit" /> Edit Current
                </button>
            </div>
        </div>
        <div x-show="$store.dbstrPicker.creating" x-cloak
            class="mb-4 border border-success/30 rounded-lg p-4 bg-base-200">
            <h4 class="font-semibold mb-2 text-success">New DBStr</h4>
            <div class="flex flex-col gap-3">
                <div class="form-control w-32">
                    <label class="label text-xs">ID</label>
                    <input
                        type="number"
                        class="input w-full"
                        x-model.number="$store.dbstrPicker.createId"
                    />
                </div>
                <div class="form-control">
                    <label class="label text-xs">Value</label>
                    <textarea
                        class="textarea w-full"
                        rows="2"
                        x-model="$store.dbstrPicker.createValue"
                        placeholder="Description text..."></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-3">
                <button type="button" class="btn btn-sm btn-soft"
                    @click="$store.dbstrPicker.cancelCreate()">Cancel</button>
                <button type="button" class="btn btn-sm btn-soft btn-success" @click="$store.dbstrPicker.saveCreate()"
                    :disabled="$store.dbstrPicker.saving || !$store.dbstrPicker.createValue.trim()">
                    <span x-show="$store.dbstrPicker.saving" class="loading loading-spinner loading-xs"></span>
                    Save & Use
                </button>
            </div>
        </div>

        <div x-show="$store.dbstrPicker.editing" x-cloak
            class="mb-4 border border-info/30 rounded-lg p-4 bg-base-200">
            <h4 class="font-semibold mb-2 text-info">Edit DBStr</h4>
            <div class="flex flex-col gap-3">
                <div class="form-control w-32">
                    <label class="label text-xs">ID</label>
                    <input
                        type="number"
                        class="input w-full"
                        x-model.number="$store.dbstrPicker.editId"
                        readonly
                    />
                </div>
                <div class="form-control">
                    <label class="label text-xs">Value</label>
                    <textarea
                        class="textarea w-full"
                        rows="3"
                        x-model="$store.dbstrPicker.editValue"
                        placeholder="Description text..."></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-3">
                <button type="button" class="btn btn-sm btn-soft"
                    @click="$store.dbstrPicker.cancelEdit()">Cancel</button>
                <button type="button" class="btn btn-sm btn-soft btn-success" @click="$store.dbstrPicker.saveEdit()"
                    :disabled="$store.dbstrPicker.saving || !$store.dbstrPicker.editValue.trim()">
                    <span x-show="$store.dbstrPicker.saving" class="loading loading-spinner loading-xs"></span>
                    Save
                </button>
            </div>
        </div>
        <div class="flex-1 overflow-y-auto bg-base-100 border border-base-content/10 rounded">
            <table class="table table-sm table-zebra w-full table-pin-rows">
                <thead class="text-xs uppercase bg-neutral">
                    <tr>
                        <th class="w-[10%]">ID</th>
                        <th>Value</th>
                        <th class="w-[5%]"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="$store.dbstrPicker.loading">
                        <tr>
                            <td colspan="3" class="text-center py-8">
                                <span class="loading loading-spinner loading-md"></span>
                            </td>
                        </tr>
                    </template>
                    <template x-if="!$store.dbstrPicker.loading && $store.dbstrPicker.results.length === 0">
                        <tr>
                            <td colspan="3" class="text-center py-8 opacity-50">No results found.</td>
                        </tr>
                    </template>
                    <template x-for="row in $store.dbstrPicker.results" :key="row.id">
                        <tr :data-dbstr-id="row.id" class="cursor-pointer hover:bg-primary/10" @click="$store.dbstrPicker.select(row.id)">
                            <td class="text-xs" x-text="row.id"></td>
                            <td class="text-sm whitespace-pre-wrap" x-text="row.value"></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-soft btn-accent">Use</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        <div class="flex items-center justify-between mt-3" x-show="$store.dbstrPicker.lastPage > 1" x-cloak>
            <button type="button" class="btn btn-sm" :disabled="$store.dbstrPicker.page <= 1"
                @click="$store.dbstrPicker.search($store.dbstrPicker.page - 1)">
                &laquo; Prev
            </button>
            <span class="text-sm opacity-70"
                x-text="`Page ${$store.dbstrPicker.page} of ${$store.dbstrPicker.lastPage}`"></span>
            <button type="button" class="btn btn-sm" :disabled="$store.dbstrPicker.page >= $store.dbstrPicker.lastPage"
                @click="$store.dbstrPicker.search($store.dbstrPicker.page + 1)">
                Next &raquo;
            </button>
        </div>
        <div class="modal-action">
            <button type="button" class="btn" @click="$store.dbstrPicker.close()">Close</button>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop"><button>close</button></form>
</dialog>
