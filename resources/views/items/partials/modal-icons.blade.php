<input type="checkbox" id="icon-picker-modal" class="modal-toggle" />
<div class="modal" x-data>
    <div class="modal-box max-w-5xl">
        <h3 class="font-bold text-lg mb-3">Select Item Icon</h3>

        <div class="flex items-center gap-3 mb-4">
            <input type="text" placeholder='Filter (e.g. 1000)' class="input input-bordered w-full max-w-xs"
                x-model="$store.iconPicker.filter" @input="$store.iconPicker.applyFilter()" />
            <span class="text-sm opacity-70" x-text="$store.iconPicker.filteredIcons.length + ' icons'"></span>
        </div>

        <div class="grid grid-flow-row auto-rows-[40px] grid-cols-[repeat(auto-fill,40px)] gap-2 min-h-[60vh] max-h-[60vh] overflow-auto"
            @scroll="$store.iconPicker.onScroll($event)">
            <template x-for="id in $store.iconPicker.visibleIcons" :key="id">
                <button class="w-10 h-10 border border-base-content/20 rounded bg-base-200 cursor-pointer"
                    @click="$store.iconPicker.select(id)" :title="id">
                    <div :class="'item-icon item-' + id"></div>
                </button>
            </template>
        </div>

        <div class="modal-action">
            <label class="btn" @click="$store.iconPicker.close()">Close</label>
        </div>
    </div>
</div>
