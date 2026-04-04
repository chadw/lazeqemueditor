<div
    x-cloak
    x-data
    x-show="$store.objectPicker.isOpen"
    class="modal modal-open"
    x-transition
>
    <div class="modal-box w-full max-w-7xl h-[90vh] flex flex-col relative">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-lg">Select Object Model</h3>
            <button
                type="button"
                class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4"
                @click="$store.objectPicker.close()"
            >
                ✕
            </button>
        </div>
        <div
            class="flex-1 overflow-y-auto overflow-x-hidden pr-1"
        >
            <div class="grid grid-cols-[repeat(auto-fill,minmax(150px,1fr))] gap-4">
                <template x-for="id in $store.objectPicker.objects" :key="id">
                    <div
                        :data-object-id="id"
                        class="relative cursor-pointer rounded-lg border border-base-300
                            bg-base-200 hover:border-primary hover:shadow-lg
                            transition-all duration-150"
                        :class="{
                            'border-2 border-secondary shadow-[0_0_12px_var(--color-secondary)]':
                                id === $store.objectPicker.selectedId
                        }"
                        style="width:150px; height:150px;"
                        @click="$store.objectPicker.select(id)"
                    >

                        <div class="absolute inset-0 flex items-center justify-center overflow-hidden p-2">
                            <div
                                :class="'object-icon object-' + id"
                                class="max-w-full max-h-full"
                                style="transform: scale(.9); transform-origin:center;"
                            ></div>
                        </div>

                        <div
                            class="absolute top-1 left-1 text-xs px-2 py-0.5
                                rounded bg-base-300/90 text-base-content shadow"
                        >
                            <span x-text="'IT' + id"></span>
                        </div>

                    </div>
                </template>
            </div>
        </div>
        <div class="modal-action">
            <button type="button" class="btn btn-soft" @click="$store.objectPicker.close()">
                Close
            </button>
        </div>
    </div>
    <div class="modal-backdrop" @click="$store.objectPicker.close()"></div>
</div>