<div data-ability="aerampage" x-data="floatingPanel()" class="relative inline-block">
    <div class="flex items-center gap-2">
        <label class="label cursor-pointer gap-2">
            <input type="checkbox" @change="$store.specialAbilities.toggleByKey('aerampage', $el)"
                class="checkbox checkbox-sm checked:checkbox-success"
                :checked="$store.specialAbilities.enabled('aerampage')" />
            <span class="label-text">AE Rampage <span class="text-xs text-neutral-400">(4)</span></span>
        </label>

        <button x-ref="trigger" type="button" class="btn btn-xs btn-soft btn-circle" @click="openPanel()"
            :class="{ 'btn-info': $store.specialAbilities.enabled('aerampage') }">
            <svg class="w-4 h-4" viewBox="0 0 20 20" fill="none" stroke-width="2.5" stroke="currentColor">
                <path d="M5 7l5 5 5-5" />
            </svg>
        </button>
        <span class="ml-2 text-sm text-muted">
            <template
                x-text="$store.specialAbilities.enabled('aerampage') ?
                    ($store.specialAbilities.items[1]?.join(',') ?? 'active') : 'disabled'">
            </template>
        </span>
    </div>

    <div x-ref="panel" x-show="open" x-cloak @click.outside="open = false"
        class="z-20 bg-base-100 p-3 rounded border-base-content/10 shadow-[0_0_12px_var(--color-info)] w-5xl">
        <h2 class="card-title mb-2">AE Rampage Parameters</h2>
        <div class="grid grid-cols-7 gap-4">
            <div class="tooltip" data-tip="Rampage target count">
                <input type="number" data-param-index="1" class="input w-full" min="0"
                    max="100" placeholder="0"
                    @input="$store.specialAbilities.updateParam('aerampage', 1, $event.target.value)" />
            </div>
            <div class="tooltip" data-tip="% of normal attack damage">
                <input type="number" data-param-index="2" class="input w-full" min="0"
                    placeholder="100"
                    @input="$store.specialAbilities.updateParam('aerampage', 2, $event.target.value)" />
            </div>
            <div class="tooltip" data-tip="Flat damage bonus to add">
                <input type="number" data-param-index="3" class="input w-full" min="0"
                    placeholder="0"
                    @input="$store.specialAbilities.updateParam('aerampage', 3, $event.target.value)" />
            </div>
            <div class="tooltip" data-tip="Ignore % armor for this attack">
                <input type="number" data-param-index="4" class="input w-full" min="0"
                    placeholder="0"
                    @input="$store.specialAbilities.updateParam('aerampage', 4, $event.target.value)" />
            </div>
            <div class="tooltip" data-tip="Ignore flat armor for this attack">
                <input type="number" data-param-index="5" class="input w-full" min="0"
                    placeholder="0"
                    @input="$store.specialAbilities.updateParam('aerampage', 5, $event.target.value)" />
            </div>
            <div class="tooltip" data-tip="% NPC Crit against">
                <input type="number" data-param-index="6" class="input w-full" min="0"
                    placeholder="100"
                    @input="$store.specialAbilities.updateParam('aerampage', 6, $event.target.value)" />
            </div>
            <div class="tooltip" data-tip="Flat crit bonus on top of npc's natual crit that can go toward this attack">
                <input type="number" data-param-index="7" class="input w-full" min="0"
                    placeholder="0"
                    @input="$store.specialAbilities.updateParam('aerampage', 7, $event.target.value)" />
            </div>
        </div>
    </div>
</div>
