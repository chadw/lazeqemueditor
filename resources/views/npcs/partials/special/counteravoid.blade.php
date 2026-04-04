<div data-ability="counteravoid" x-data="floatingPanel()" class="relative inline-block">
    <div class="flex items-center gap-2">
        <label class="label cursor-pointer gap-2">
            <input type="checkbox" @change="$store.specialAbilities.toggleByKey('counteravoid', $el)"
                class="checkbox checkbox-sm checked:checkbox-success"
                :checked="$store.specialAbilities.enabled('counteravoid')" />
            <span class="label-text">Counter Avoid Damage <span class="text-xs text-neutral-400">(44)</span></span>
        </label>

        <button x-ref="trigger" type="button" class="btn btn-xs btn-soft btn-circle" @click="openPanel()"
            :class="{ 'btn-info': $store.specialAbilities.enabled('counteravoid') }">
            <svg class="w-4 h-4" viewBox="0 0 20 20" fill="none" stroke-width="2.5" stroke="currentColor">
                <path d="M5 7l5 5 5-5" />
            </svg>
        </button>
        <span class="ml-2 text-sm text-muted">
            <template
                x-text="$store.specialAbilities.enabled('counteravoid') ?
                    ($store.specialAbilities.items[1]?.join(',') ?? 'active') : 'disabled'">
            </template>
        </span>
    </div>

    <div x-ref="panel" x-show="open" x-cloak @click.outside="open = false"
        class="absolute z-20 mt-2 bg-base-100 p-3 rounded border-base-content/10 shadow-[0_0_12px_var(--color-info)] w-xl">
        <h2 class="card-title mb-2">Counter Avoid Damage</h2>
        <div class="grid grid-cols-5 gap-4">
            <div class="tooltip" data-tip="% Reduction to avoid melee via Riposte, Parry, Block, and Dodge">
                <input type="number" data-param-index="1" class="input w-full" min="0"
                    placeholder="0"
                    @input="$store.specialAbilities.updateParam('counteravoid', 1, $event.target.value)" />
            </div>
            <div class="tooltip" data-tip="% Reduction to Riposte">
                <input type="number" data-param-index="2" class="input w-full" min="0"
                    placeholder="0"
                    @input="$store.specialAbilities.updateParam('counteravoid', 2, $event.target.value)" />
            </div>
            <div class="tooltip" data-tip="% Reduction to Block">
                <input type="number" data-param-index="3" class="input w-full" min="0"
                    placeholder="0"
                    @input="$store.specialAbilities.updateParam('counteravoid', 3, $event.target.value)" />
            </div>
            <div class="tooltip" data-tip="% Reduction to Parry">
                <input type="number" data-param-index="4" class="input w-full" min="0"
                    placeholder="0"
                    @input="$store.specialAbilities.updateParam('counteravoid', 4, $event.target.value)" />
            </div>
            <div class="tooltip" data-tip="% Reduction to Dodge">
                <input type="number" data-param-index="5" class="input w-full" min="0"
                    placeholder="0"
                    @input="$store.specialAbilities.updateParam('counteravoid', 5, $event.target.value)" />
            </div>
        </div>
    </div>
</div>
