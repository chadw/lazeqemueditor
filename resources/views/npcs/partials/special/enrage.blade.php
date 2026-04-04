<div data-ability="enrage" x-data="floatingPanel()" class="relative inline-block">
    <div class="flex items-center gap-2">
        <label class="label cursor-pointer gap-2">
            <input type="checkbox" @change="$store.specialAbilities.toggleByKey('enrage', $el)"
                class="checkbox checkbox-sm checked:checkbox-success"
                :checked="$store.specialAbilities.enabled('enrage')" />
            <span class="label-text">Enrage <span class="text-xs text-neutral-400">(2)</span></span>
        </label>

        <button x-ref="trigger" type="button" class="btn btn-xs btn-soft btn-circle" @click="openPanel()"
            :class="{ 'btn-info': $store.specialAbilities.enabled('enrage') }">
            <svg class="w-4 h-4" viewBox="0 0 20 20" fill="none" stroke-width="2.5" stroke="currentColor">
                <path d="M5 7l5 5 5-5" />
            </svg>
        </button>
        <span class="ml-2 text-sm text-muted">
            <template
                x-text="$store.specialAbilities.enabled('enrage') ?
                    ($store.specialAbilities.items[1]?.join(',') ?? 'active') : 'disabled'">
            </template>
        </span>
    </div>

    <div x-ref="panel" x-show="open" x-cloak @click.outside="open = false"
        class="absolute z-20 mt-2 bg-base-100 p-3 rounded border-base-content/10 shadow-[0_0_12px_var(--color-info)] w-xl">
        <h2 class="card-title mb-2">Enrage</h2>
        <div class="grid grid-cols-3 gap-4">
            <div class="tooltip" data-tip="HP % to Enrage (rule NPC:StartEnrageValue)">
                <input type="number" data-param-index="1" class="input w-full" min="0"
                    max="100" placeholder="0"
                    @input="$store.specialAbilities.updateParam('enrage', 1, $event.target.value)" />
            </div>
            <div class="tooltip" data-tip="Duration (ms) (10000)">
                <input type="number" data-param-index="2" class="input w-full"
                    placeholder="10000"
                    @input="$store.specialAbilities.updateParam('enrage', 2, $event.target.value)" />
            </div>
            <div class="tooltip" data-tip="Cooldown (ms) (360000)">
                <input type="number" data-param-index="3" class="input w-full"
                    placeholder="360000"
                    @input="$store.specialAbilities.updateParam('enrage', 3, $event.target.value)" />
            </div>
        </div>
    </div>
</div>
