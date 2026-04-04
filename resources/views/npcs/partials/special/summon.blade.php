<div data-ability="summon" x-data="floatingPanel()" class="relative inline-block">
    <div class="flex items-center gap-2">
        <label class="label cursor-pointer gap-2">
            <input type="checkbox" @change="$store.specialAbilities.toggleByKey('summon', $el)"
                class="checkbox checkbox-sm checked:checkbox-success"
                :checked="$store.specialAbilities.enabled('summon')" />
            <span class="label-text">Summon <span class="text-xs text-neutral-400">(1)</span></span>
        </label>

        <button x-ref="trigger" type="button" class="btn btn-xs btn-soft btn-circle" @click="openPanel()"
            :class="{ 'btn-info': $store.specialAbilities.enabled('summon') }">
            <svg class="w-4 h-4" viewBox="0 0 20 20" fill="none" stroke-width="2.5" stroke="currentColor">
                <path d="M5 7l5 5 5-5" />
            </svg>
        </button>
        <span class="ml-2 text-sm text-muted">
            <template
                x-text="$store.specialAbilities.enabled('summon') ?
                    ($store.specialAbilities.items[1]?.join(',') ?? 'active') : 'disabled'">
            </template>
        </span>
    </div>

    <div x-ref="panel" x-show="open" x-cloak @click.outside="open = false"
        class="absolute z-20 mt-2 bg-base-100 p-3 rounded border-base-content/10 shadow-[0_0_12px_var(--color-info)] w-xl">
        <h2 class="card-title mb-2">Summon</h2>
        <div class="grid grid-cols-4 gap-4">
            <div class="col-span-2 tooltip" data-tip="Summon type">
                <select class="select w-full" data-param-index="0"
                    @change="$store.specialAbilities.updateParam('summon', 0, $event.target.value)">
                    <option value="1">Summon Target to NPC</option>
                    <option value="2">Summon NPC to Target</option>
                </select>
            </div>
            <div class="tooltip" data-tip="Cooldown in ms (default: 6000)">
                <input type="number" data-param-index="1" class="input w-full"
                    placeholder="6000"
                    @input="$store.specialAbilities.updateParam('summon', 1, $event.target.value)" />
            </div>
            <div class="tooltip" data-tip="HP % before summon (default: 97)">
                <input type="number" data-param-index="2" class="input w-full" min="0"
                    max="100" placeholder="97"
                    @input="$store.specialAbilities.updateParam('summon', 2, $event.target.value)" />
            </div>
        </div>
    </div>
</div>
