<div x-data="iconPicker()" x-cloak class="z-10">
    <button type="button" @click="open()" class="btn btn-sm join-item">Choose Icon</button>

    <div class="modal" :class="{ 'modal-open': show }" aria-hidden="!show">
        <div class="modal-box w-full max-w-5xl min-h-8/12 max-h-8/12">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-lg">Select Item Icon</h3>
                <button type="button" class="btn btn-ghost btn-sm" @click="close()">Close</button>
            </div>

            <div class="mt-4">
                <div class="flex items-center gap-3">
                    <input x-model="filter" type="search" placeholder="Filter (e.g. 1000 or item-1023)" class="input input-bordered input-sm w-full" />
                    <div class="text-sm text-gray-500" x-text="count + ' icons'"></div>
                </div>

                <div class="mt-4">
                    <div x-show="_loading" class="py-8 flex items-center justify-center">
                        <div class="text-sm">Loading icons…</div>
                    </div>

                    <div x-show="!_loading">
                        <div x-show="_rawHtml" class="grid grid-cols-20 gap-2 icon-grid-raw"></div>

                        <div x-show="!_rawHtml" class="grid grid-cols-20 gap-2">
                            <template x-for="cls in classes" :key="cls">
                                <div class="itemcard cursor-pointer flex items-center justify-center" @click="select(cls.replace('item-',''))">
                                    <div :class="`item-icon ${cls}`" :title="cls.replace('item-','')"></div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-action">
                <button type="button" class="btn" @click="close()">Done</button>
            </div>
        </div>
    </div>
</div>
