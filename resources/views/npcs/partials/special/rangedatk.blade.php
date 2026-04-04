<div data-ability="rangedatk" x-data="floatingPanel()" class="relative inline-block">
    <div class="flex items-center gap-2">
        <label class="label cursor-pointer gap-2">
            <input type="checkbox" @change="$store.specialAbilities.toggleByKey('rangedatk', $el)"
                class="checkbox checkbox-sm checked:checkbox-success"
                :checked="$store.specialAbilities.enabled('rangedatk')" />
            <span class="label-text">Ranged Attack <span class="text-xs text-neutral-400">(11)</span></span>
        </label>

        <button x-ref="trigger" type="button" class="btn btn-xs btn-soft btn-circle" @click="openPanel()"
            :class="{ 'btn-info': $store.specialAbilities.enabled('rangedatk') }">
            <svg class="w-4 h-4" viewBox="0 0 20 20" fill="none" stroke-width="2.5" stroke="currentColor">
                <path d="M5 7l5 5 5-5" />
            </svg>
        </button>
        <span class="ml-2 text-sm text-muted">
            <template
                x-text="$store.specialAbilities.enabled('rangedatk') ?
                    ($store.specialAbilities.items[1]?.join(',') ?? 'active') : 'disabled'">
            </template>
        </span>
    </div>

    <div x-ref="panel" x-show="open" x-cloak @click.outside="open = false"
        class="absolute z-20 mt-2 bg-base-100 p-3 rounded border-base-content/10 shadow-[0_0_12px_var(--color-info)] w-xl">
        <h2 class="card-title mb-2">Ranged Attack</h2>
        <div class="grid grid-cols-5 gap-4">
            <div class="tooltip" data-tip="Number of Attacks">
                <input type="number" data-param-index="1" class="input w-full" min="0"
                    placeholder="0"
                    @input="$store.specialAbilities.updateParam('rangedatk', 1, $event.target.value)" />
            </div>
            <div class="tooltip" data-tip="Max Range (default: 250)">
                <input type="number" data-param-index="2" class="input w-full"
                    placeholder="250"
                    @input="$store.specialAbilities.updateParam('rangedatk', 2, $event.target.value)" />
            </div>
            <div class="tooltip" data-tip="Percent Hit Chance Modifier">
                <input type="number" data-param-index="3" class="input w-full"
                    placeholder="0"
                    @input="$store.specialAbilities.updateParam('rangedatk', 3, $event.target.value)" />
            </div>
            <div class="tooltip" data-tip="Percent Damage Modifier">
                <input type="number" data-param-index="4" class="input w-full"
                    placeholder="0"
                    @input="$store.specialAbilities.updateParam('rangedatk', 4, $event.target.value)" />
            </div>
            <div class="tooltip" data-tip="Min Range (default: RuleI(Combat, MinRangedAttackDist) = 25)">
                <input type="number" data-param-index="5" class="input w-full"
                    placeholder="25"
                    @input="$store.specialAbilities.updateParam('rangedatk', 5, $event.target.value)" />
            </div>
        </div>
    </div>
</div>
