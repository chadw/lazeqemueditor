<div class="space-y-6">
    <div class="grid grid-cols-1 lg:grid-cols-1 xl:grid-cols-2 gap-4">
        <div class="card bg-base-200 card-sm shadow-sm">
            <div class="card-body">
                <div class="flex items-center justify-between border-b border-base-300 pb-2 mb-4">
                    <h2 class="card-title">Primary Stats</h2>
                    <span class="text-xs text-base-content/50 uppercase">
                        Strength, Stamina, Intelligence, Wisdom, Agility, Dexterity, Charisma
                    </span>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-4 xl:grid-cols-7 gap-4">
                    <x-form.input
                        name="STR"
                        label="Strength"
                        type="number"
                        min="0"
                        :value="$npc->STR"
                    />
                    <x-form.input
                        name="STA"
                        label="Stamina"
                        type="number"
                        min="0"
                        :value="$npc->STA"
                    />
                    <x-form.input
                        name="AGI"
                        label="Agility"
                        type="number"
                        min="0"
                        :value="$npc->AGI"
                    />
                    <x-form.input
                        name="DEX"
                        label="Dexterity"
                        type="number"
                        min="0"
                        :value="$npc->DEX"
                    />
                    <x-form.input
                        name="WIS"
                        label="Wisdom"
                        type="number"
                        min="0"
                        :value="$npc->WIS"
                    />
                    <x-form.input
                        name="_INT"
                        label="Intelligence"
                        type="number"
                        min="0"
                        :value="$npc->_INT"
                    />
                    <x-form.input
                        name="CHA"
                        label="Charisma"
                        type="number"
                        min="0"
                        :value="$npc->CHA"
                    />
                </div>
            </div>
        </div>
        <div class="card bg-base-200 card-sm shadow-sm">
            <div class="card-body">
                <div class="flex items-center justify-between border-b border-base-300 pb-2 mb-4">
                    <h2 class="card-title">Resists</h2>
                    <span class="hidden md:inline text-xs text-base-content/50 uppercase">
                        Elemental Defensive Values
                    </span>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-4 xl:grid-cols-7 gap-4">
                    <x-form.input
                        name="MR"
                        label="Magic"
                        type="number"
                        min="0"
                        :value="$npc->MR"
                        label-class="text-violet-500!"
                    />
                    <x-form.input
                        name="FR"
                        label="Fire"
                        type="number"
                        min="0"
                        :value="$npc->FR"
                        label-class="text-red-500!"
                    />
                    <x-form.input
                        name="CR"
                        label="Cold"
                        type="number"
                        min="0"
                        :value="$npc->CR"
                        label-class="text-sky-500!"
                    />
                    <x-form.input
                        name="DR"
                        label="Disease"
                        type="number"
                        min="0"
                        :value="$npc->DR"
                        label-class="text-lime-600!"
                    />
                    <x-form.input
                        name="PR"
                        label="Poison"
                        type="number"
                        min="0"
                        :value="$npc->PR"
                        label-class="text-green-600!"
                    />
                    <x-form.input
                        name="Corrup"
                        label="Corruption"
                        type="number"
                        min="0"
                        :value="$npc->Corrup"
                        label-class="text-rose-600!"
                    />
                    <x-form.input
                        name="PhR"
                        label="Physical"
                        type="number"
                        min="0"
                        :value="$npc->PhR"
                        label-class="text-yellow-600!"
                    />
                </div>
            </div>
        </div>
        <div class="card bg-base-200 card-sm shadow-sm">
            <div class="card-body">
                <div class="flex items-center justify-between border-b border-base-300 pb-2 mb-4">
                    <h2 class="card-title">Charmed Stats</h2>
                    <span class="text-xs text-base-content/50 uppercase">
                        Applied only while the NPC is charmed, these values override the NPC's base stats.
                    </span>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-4 xl:grid-cols-7 gap-4">
                    <x-form.input
                        name="charm_min_dmg"
                        label="Min Damage"
                        type="number"
                        min="0"
                        :value="$npc->charm_min_dmg"
                    />
                    <x-form.input
                        name="charm_max_dmg"
                        label="Max Damage"
                        type="number"
                        min="0"
                        :value="$npc->charm_max_dmg"
                    />
                    <x-form.input
                        name="charm_attack_delay"
                        label="Attack Delay"
                        type="number"
                        min="0"
                        :value="$npc->charm_attack_delay"
                    />
                    <x-form.input
                        name="charm_atk"
                        label="Attack"
                        type="number"
                        min="0"
                        :value="$npc->charm_atk"
                    />
                    <x-form.input
                        name="charm_accuracy_rating"
                        label="Accuracy"
                        type="number"
                        min="0"
                        :value="$npc->charm_accuracy_rating"
                    />
                    <x-form.input
                        name="charm_ac"
                        label="AC"
                        type="number"
                        min="0"
                        :value="$npc->charm_ac"
                    />
                    <x-form.input
                        name="charm_avoidance_rating"
                        label="Avoidance"
                        type="number"
                        min="0"
                        :value="$npc->charm_avoidance_rating"
                    />
                </div>
            </div>
        </div>
    </div>
</div>
