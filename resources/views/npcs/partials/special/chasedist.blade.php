<div data-ability="chasedist" x-data="floatingPanel()" class="relative inline-block">
    <div class="flex items-center gap-2">
        <label class="label cursor-pointer gap-2">
            <input type="checkbox" @change="$store.specialAbilities.toggleByKey('chasedist', $el)"
                class="checkbox checkbox-sm checked:checkbox-success"
                :checked="$store.specialAbilities.enabled('chasedist')" />
            <span class="label-text">Chase Distance <span class="text-xs text-neutral-400">(40)</span></span>
        </label>

        <button x-ref="trigger" type="button" class="btn btn-xs btn-soft btn-circle" @click="openPanel()"
            :class="{ 'btn-info': $store.specialAbilities.enabled('chasedist') }">
            <svg class="w-4 h-4" viewBox="0 0 20 20" fill="none" stroke-width="2.5" stroke="currentColor">
                <path d="M5 7l5 5 5-5" />
            </svg>
        </button>
        <span class="ml-2 text-sm text-muted">
            <template
                x-text="$store.specialAbilities.enabled('chasedist') ?
                    ($store.specialAbilities.items[1]?.join(',') ?? 'active') : 'disabled'">
            </template>
        </span>
    </div>

    <div x-ref="panel" x-show="open" x-cloak @click.outside="open = false"
        class="absolute z-20 mt-2 bg-base-100 p-3 rounded border-base-content/10 shadow-[0_0_12px_var(--color-info)] w-md">
        <h2 class="card-title mb-2">Chase Distance</h2>
        <div class="grid grid-cols-3 gap-4">
            <div class="tooltip" data-tip="Max Chase Distance">
                <input type="number" data-param-index="1" class="input w-full"
                    placeholder="0"
                    @input="$store.specialAbilities.updateParam('chasedist', 1, $event.target.value)" />
            </div>
            <div class="tooltip" data-tip="Min Chase Distance">
                <input type="number" data-param-index="2" class="input w-full" min="0"
                    placeholder="0"
                    @input="$store.specialAbilities.updateParam('chasedist', 2, $event.target.value)" />
            </div>
            <div class="tooltip" data-tip="Ignore line of sight check for chasing">
                <input type="number" data-param-index="3" class="input w-full" min="0"
                    placeholder="0"
                    @input="$store.specialAbilities.updateParam('chasedist', 3, $event.target.value)" />
            </div>
        </div>
    </div>
</div>
