<div x-data x-cloak x-show="$store.idPicker.isOpen">
    <div class="fixed inset-0 z-9999">
        <div class="absolute inset-0 bg-neutral/80 backdrop-blur cursor-pointer" @click="$store.idPicker.close()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="w-full max-w-3xl bg-base-100 rounded-lg shadow-xl overflow-hidden">
                <div class="flex justify-between items-center p-3 border-b border-base-content/10">
                    <h3 class="font-semibold">Available ID Blocks</h3>
                    <button type="button" class="btn btn-soft btn-ghost btn-sm"
                        @click="$store.idPicker.close()">✕</button>
                </div>

                <div class="p-3 max-h-[60vh] overflow-auto">
                    <template x-for="b in $store.idPicker.blocks" :key="b.start">
                        <div class="flex justify-between items-center py-2 border-b border-base-content/10">
                            <div class="text-sm">
                                <strong x-text="b.start"></strong>
                                <span class="ml-2 text-neutral-400" x-text="b.count + ' free'"></span>
                            </div>
                            <div class="flex gap-2">
                                <button type="button" class="btn btn-soft btn-xs"
                                    @click="$store.idPicker.apply(b.start)">Use <span x-text="b.start"></span></button>
                                <button type="button" class="btn btn-soft btn-xs"
                                    @click="$store.idPicker.apply(b.end)">Use <span x-text="b.end"></span></button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>
