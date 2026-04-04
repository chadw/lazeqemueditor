<div class="space-y-6">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        <div class="card bg-base-200 card-sm shadow-sm">
            <div class="card-body">
                <div class="flex items-center justify-between border-b border-base-300 pb-2 mb-4">
                    <h2 class="card-title">Visuals</h2>
                    <span class="text-xs text-base-content/50 uppercase">Model & textures</span>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <x-form.input
                        name="size"
                        label="Size"
                        type="number"
                        :value="$npc->size"
                    />
                    <x-form.input
                        name="model"
                        label="Model"
                        type="number"
                        :value="$npc->model"
                    />
                    <x-form.input
                        name="texture"
                        label="Texture"
                        type="number"
                        :value="$npc->texture"
                    />
                    <x-form.input
                        name="helmtexture"
                        label="Helm Texture"
                        type="number"
                        :value="$npc->helmtexture"
                    />
                    <x-form.input
                        name="herosforgemodel"
                        label="Hero's Forge Model"
                        type="number"
                        :value="$npc->herosforgemodel"
                    />
                    <x-form.input
                        name="light"
                        label="Light Source"
                        type="number"
                        :value="$npc->light"
                    />
                    <x-form.input
                        name="npc_tint_id"
                        label="NPC Tint ID"
                        type="number"
                        :value="$npc->npc_tint_id"
                    />
                    <div></div>
                </div>
            </div>
        </div>

        <div class="card bg-base-200 card-sm shadow-sm">
            <div class="card-body">
                <div class="flex items-center justify-between border-b border-base-300 pb-2 mb-4">
                    <h2 class="card-title">Hair & Face</h2>
                    <span class="text-xs text-base-content/50 uppercase">Appearance details</span>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <x-form.input name="face" label="Face" type="number" :value="$npc->face" />
                    <x-form.input name="luclin_hairstyle" label="Hairstyle" type="number" :value="$npc->luclin_hairstyle" />
                    <x-form.input name="luclin_haircolor" label="Hair Color" type="number" :value="$npc->luclin_haircolor" />
                    <x-form.input name="luclin_eyecolor" label="Eye Color" type="number" :value="$npc->luclin_eyecolor" />
                    <x-form.input name="luclin_eyecolor2" label="Eye Color 2" type="number" :value="$npc->luclin_eyecolor2" />
                    <x-form.input name="luclin_beard" label="Beard" type="number" :value="$npc->luclin_beard" />
                    <x-form.input name="luclin_beardcolor" label="Beard Color" type="number" :value="$npc->luclin_beardcolor" />
                    <div></div>
                </div>
            </div>
        </div>
        <div class="card bg-base-200 card-sm shadow-sm">
            <div class="card-body">
                <div class="flex items-center justify-between border-b border-base-300 pb-2 mb-4">
                    <h2 class="card-title">Combat / Melee</h2>
                    <span class="text-xs text-base-content/50 uppercase">Weapons & slots</span>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div x-data="Object.assign(modelPreview('{{ $npc->d_melee_texture1 }}'), { popoverOpen: false })"
                        class="join flex items-center gap-2"
                    >
                        <x-form.input
                            name="d_melee_texture1"
                            label="Melee 1 Model"
                            tooltip="This defines how an item will look when equiped or dropped on the ground."
                            :value="$npc->d_melee_texture1"
                            class="join-item input"
                            x-ref="input"
                            type="number"
                            @input="updateFromInput($event.target.value)"
                        />
                        <button
                            type="button"
                            class="join-item btn btn-soft btn-secondary mt-4"
                            @click="$store.objectPicker.open('#d_melee_texture1', {{ Js::from($objectIds) }})"
                        >
                            Pick
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
                    <div x-data="Object.assign(modelPreview('{{ $npc->d_melee_texture2 }}'), { popoverOpen: false })"
                        class="join flex items-center gap-2"
                    >
                        <x-form.input
                            name="d_melee_texture2"
                            label="Melee 2 Model"
                            tooltip="This defines how an item will look when equiped or dropped on the ground."
                            :value="$npc->d_melee_texture2"
                            class="join-item input"
                            x-ref="input"
                            type="number"
                            @input="updateFromInput($event.target.value)"
                        />
                        <button
                            type="button"
                            class="join-item btn btn-soft btn-secondary mt-4"
                            @click="$store.objectPicker.open('#d_melee_texture2', {{ Js::from($objectIds) }})"
                        >
                            Pick
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
                    <div x-data="Object.assign(modelPreview('{{ $npc->ammo_idfile }}'), { popoverOpen: false })"
                        class="join flex items-center gap-2"
                    >
                        <x-form.input
                            name="ammo_idfile"
                            label="Ammo Model"
                            tooltip="This defines how an item will look when equiped or dropped on the ground."
                            :value="$npc->ammo_idfile"
                            class="join-item input"
                            x-ref="input"
                            @input="updateFromInput($event.target.value)"
                        />
                        <button
                            type="button"
                            class="join-item btn btn-soft btn-secondary mt-4"
                            @click="$store.objectPicker.open('#ammo_idfile', {{ Js::from($objectIds) }})"
                        >
                            Pick
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
                    <x-form.select
                        name="prim_melee_type"
                        label="Melee1 Type"
                        :options="config('everquest.db_skills')"
                        keyInOption="true"
                        :selected="$npc->prim_melee_type"
                    />
                    <x-form.select
                        name="sec_melee_type"
                        label="Melee2 Type"
                        :options="config('everquest.db_skills')"
                        keyInOption="true"
                        :selected="$npc->sec_melee_type"
                    />
                    <x-form.select
                        name="ranged_type"
                        label="Ranged Type"
                        :options="config('everquest.db_skills')"
                        keyInOption="true"
                        :selected="$npc->ranged_type"
                    />
                </div>
            </div>
        </div>
        <div class="card bg-base-200 card-sm shadow-sm">
            <div class="card-body">
                <div class="flex items-center justify-between border-b border-base-300 pb-2 mb-4">
                    <h2 class="card-title">Cosmetics</h2>
                    <span class="text-xs text-base-content/50 uppercase">Tints & markings</span>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <x-form.input name="drakkin_heritage" label="Heritage" type="number" :value="$npc->drakkin_heritage" />
                    <x-form.input name="drakkin_tattoo" label="Tattoo" type="number" :Value="$npc->drakkin_tattoo" />
                    <x-form.input name="drakkin_details" label="Details" type="number" :value="$npc->drakkin_details" />
                    <x-form.input name="armortint_red" label="Armor Red" type="number" min="0" max="255"
                        :value="$npc->armortint_red" />
                    <x-form.input name="armortint_green" label="Armor Green" type="number" min="0"
                        max="255" :value="$npc->armortint_green" />
                    <x-form.input name="armortint_blue" label="Armor Blue" type="number" min="0"
                        max="255" :value="$npc->armortint_blue" />
                    <div class="col-span-2">
                        <label class="label">Armor Tint Preview</label>
                        <div class="w-full h-12 rounded border border-base-content/5"
                            style="background: rgba({{ $npc->armortint_red ?? 0 }}, {{ $npc->armortint_green ?? 0 }}, {{ $npc->armortint_blue ?? 0 }}, 0.25);">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
