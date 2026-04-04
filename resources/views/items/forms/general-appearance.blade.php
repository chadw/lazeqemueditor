<div class="card bg-base-200 card-sm shadow-sm">
    <div class="card-body">
        <h2 class="card-title">Appearance</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-6 gap-4">
            <x-form.select
                name="size"
                label="Item Size"
                tooltip="This is the maximum stack size used for stackable items."
                :options="config('everquest.item_size')"
                :selected="$item->size"
            />
            <div
                x-data="{ value: '{{ $item->icon }}' }"
                class="join flex items-center gap-2 mt-4"
            >
                <div
                    class="join-item w-10 h-10 min-w-10 border rounded border-base-content/20 bg-base-200"
                    :class="value ? 'item-icon item-' + value : ''"
                ></div>

                <input
                    id="itemIcon"
                    name="icon"
                    type="text"
                    class="join-item input"
                    x-model="value"
                />

                <button
                    type="button"
                    class="join-item btn btn-soft btn-secondary"
                    @click="$store.iconPicker.open('itemIcon')"
                >
                    <x-ui.icon name="search" />
                </button>
            </div>
            <div x-data="argbColorPicker({{ json_encode($item->color ?? 0) }})" x-init="init()" class="flex items-center gap-3">
                <div class="w-full">
                    <label class="label">
                        <span class="label-text">Color</span>
                        <span class="font-light text-neutral-500" x-text="dec"></span>
                    </label>
                    <input type="text" x-model="hex" @input="updateDec()"
                        class="coloris input w-full text-base-content"
                        style="background:transparent!important;" />

                    <input type="hidden" name="color" :value="dec" />
                </div>
            </div>
            <x-form.select
                name="material"
                label="Material"
                tooltip="This is the texture used for the item. Only worn armor pieces require this setting."
                :options="config('everquest.item_material')"
                :selected="$item->material"
            />
            <x-form.input
                name="herosforgemodel"
                label="Hero Forge Model"
                tooltip=""
                :value="$item->herosforgemodel"
            />
            <div x-data="Object.assign(modelPreview('{{ $item->idfile }}'), { popoverOpen: false })"
                class="join flex items-center gap-2"
            >
                <x-form.input
                    name="idfile"
                    label="Model"
                    tooltip="This defines how an item will look when equiped or dropped on the ground."
                    :value="$item->idfile"
                    class="join-item input"
                    x-ref="input"
                    @input="updateFromInput($event.target.value)"
                />
                <button
                    type="button"
                    class="join-item btn btn-soft btn-secondary mt-4"
                    @click="$store.objectPicker.open('#idfile', {{ Js::from($objectIds) }})"
                >
                    <x-ui.icon name="search" />
                </button>
                <div class="relative inline-block">
                    <button
                        type="button"
                        class="join-item btn btn-soft btn-info mt-4"
                        @click="popoverOpen = !popoverOpen"
                        :aria-expanded="popoverOpen"
                    >
                        <x-ui.icon name="show" />
                    </button>
                    <div x-cloak x-show="popoverOpen" x-transition @click.outside="popoverOpen = false"
                        class="absolute z-50 bottom-full left-1/2 transform -translate-x-1/2 -mb-1 w-auto"
                    >
                        <div class="card bg-neutral p-2 shadow-lg rounded-md relative">
                            <div class="relative w-31.25 h-31.25 rounded-lg border border-base-300
                                bg-base-200 flex items-center justify-center overflow-hidden mx-auto"
                            >
                                <template x-if="objectId">
                                    <div class="absolute inset-0 flex items-center justify-center p-2">
                                        <div :class="'object-icon object-' + objectId" class="max-w-full max-h-full"
                                            style="transform: scale(.9); transform-origin:center;"></div>
                                    </div>
                                </template>
                                <template x-if="!objectId">
                                    <div class="text-sm opacity-50">No Model</div>
                                </template>
                            </div>
                        </div>
                        <div class="absolute left-1/2 transform -translate-x-1/2 top-full -mt-1 pointer-events-none">
                            <svg width="20" height="10" viewBox="0 0 20 10" class="block text-neutral">
                                <path d="M0 0 L10 10 L20 0 Z" fill="currentColor" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
