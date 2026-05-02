<div class="space-y-6">
    <div class="grid grid-cols-3 gap-4">
        <div class="card bg-base-200 card-sm shadow-sm col-span-2">
            <div class="card-body">
                <div class="flex items-center justify-between border-b border-base-300 pb-2 mb-4">
                    <h2 class="card-title">Combat &amp; Spell Modifiers</h2>
                    <span class="text-xs text-base-content/50 uppercase">
                        Offensive, Defensive, and Regeneration modifiers.
                    </span>
                </div>
                <div class="grid grid-cols-6 gap-4">
                    <x-form.input
                        name="mindmg"
                        label="Min Damage"
                        type="number"
                        min="0"
                        :value="$npc->mindmg"
                    />
                    <x-form.input
                        name="maxdmg"
                        label="Max Damage"
                        type="number"
                        min="0"
                        :value="$npc->maxdmg"
                    />
                    <x-form.input
                        name="attack_count"
                        label="Attack Count"
                        type="number"
                        min="-1"
                        :value="$npc->attack_count"
                    />
                    <x-form.input
                        name="attack_delay"
                        label="Attack Delay"
                        type="number"
                        min="0"
                        :value="$npc->attack_delay"
                    />
                    <x-form.input
                        name="slow_mitigation"
                        label="Slow Mitigation"
                        type="number"
                        min="0"
                        :value="$npc->slow_mitigation"
                    />
                    <x-form.input
                        name="heroic_strikethrough"
                        label="Heroic Strikethrough"
                        type="number"
                        min="0"
                        :value="$npc->heroic_strikethrough"
                    />
                </div>
            </div>
        </div>
        <div class="card bg-base-200 card-sm shadow-sm">
            <div class="card-body">
                <div class="flex items-center justify-between border-b border-base-300 pb-2 mb-4">
                    <h2 class="card-title">Regen</h2>
                    <span class="text-xs text-base-content/50 uppercase"></span>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <x-form.input
                        name="hp_regen_rate"
                        label="HP Regen"
                        type="number"
                        min="0"
                        :value="$npc->hp_regen_rate"
                    />
                    <x-form.input
                        name="hp_regen_per_second"
                        label="HP Regen/sec"
                        type="number"
                        min="0"
                        :value="$npc->hp_regen_per_second"
                    />
                    <x-form.input
                        name="mana_regen_rate"
                        label="Mana Regen"
                        type="number"
                        min="0"
                        :value="$npc->mana_regen_rate"
                    />
                </div>
            </div>
        </div>
        <div class="card bg-base-200 card-sm shadow-sm">
            <div class="card-body">
                <div class="flex items-center justify-between border-b border-base-300 pb-2 mb-4">
                    <h2 class="card-title">Aggro</h2>
                    <span class="text-xs text-base-content/50 uppercase"></span>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <x-form.input
                        name="aggroradius"
                        label="Aggro Radius"
                        type="number"
                        min="0"
                        :value="$npc->aggroradius"
                    />
                    <x-form.input
                        name="assistradius"
                        label="Assist Radius"
                        type="number"
                        min="0"
                        :value="$npc->assistradius"
                    />
                </div>
            </div>
        </div>
        <div class="card bg-base-200 card-sm shadow-sm">
            <div class="card-body">
                <div class="flex items-center justify-between border-b border-base-300 pb-2 mb-4">
                    <h2 class="card-title">Scale</h2>
                    <span class="text-xs text-base-content/50 uppercase"></span>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <x-form.input
                        name="spellscale"
                        label="Spell Scale"
                        type="number"
                        min="0"
                        :value="$npc->spellscale"
                    />
                    <x-form.input
                        name="healscale"
                        label="Heal Scale"
                        type="number"
                        min="0"
                        :value="$npc->healscale"
                    />
                </div>
            </div>
        </div>
        <div class="card bg-base-200 card-sm shadow-sm">
            <div class="card-body">
                <div class="flex items-center justify-between border-b border-base-300 pb-2 mb-4">
                    <h2 class="card-title">Experience</h2>
                    <span class="text-xs text-base-content/50 uppercase"></span>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <x-form.input
                        name="exp_mod"
                        label="Exp Modifier"
                        type="number"
                        min="0"
                        :value="$npc->exp_mod"
                    />
                </div>
            </div>
        </div>
    </div>
    <div class="grid grid-cols-1 gap-4">
        @include('npcs.partials.special_abilities')
    </div>
</div>
