<div class="space-y-6">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="flex items-center justify-between border-b border-base-300 pb-2 mb-4">
                <h2 class="card-title">Base Stats</h2>
                <span class="text-xs text-base-content/50 uppercase">Core Attributes</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-4">
                <x-form.input
                    name="ac"
                    label="AC"
                    tooltip=""
                    type="number"
                    :value="$item->ac"
                />
                <x-form.input
                    name="hp"
                    label="HP"
                    tooltip="The amount of Hit Points provided by this item."
                    type="number"
                    :value="$item->hp"
                />
                <x-form.input
                    name="mana"
                    label="Mana"
                    tooltip="The amount of Mana provided by this item."
                    type="number"
                    :value="$item->mana"
                />
                <x-form.input
                    name="endur"
                    label="Endurance"
                    tooltip="The amount of Endurance provided by this item."
                    type="number"
                    :value="$item->endur"
                />
                <x-form.input
                    name="purity"
                    label="Purity"
                    tooltip=""
                    type="number"
                    :value="$item->purity"
                />
                <x-form.input
                    name="light"
                    label="Light"
                    tooltip="The amount of light given off when this item is equipped or put into a normal inventory slot (not inside a bag)"
                    type="number"
                    :value="$item->light"
                />
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="flex items-center justify-between border-b border-base-300 pb-2 mb-4">
                <h2 class="card-title">Primary Stats</h2>
                <span class="text-xs text-base-content/50 uppercase">
                    Strength, Stamina, Intelligence, Wisdom, Agility, Dexterity, Charisma
                </span>
            </div>
            <div class="grid grid-cols-4 sm:grid-cols-7 gap-4">
                <div class="rounded-lg border border-base-300 p-3 bg-base-100">
                    <div class="text-sm font-medium mb-2">STR</div>
                    <div class="grid grid-cols-2 gap-3">
                        <x-form.input
                            name="astr"
                            label="Base"
                            tooltip="Determines how much you can carry. Also adds to the damage you do with melee
                                weapons."
                            type="number"
                            min="-128"
                            max="127"
                            :value="$item->astr"
                        />
                        <x-form.input
                            name="heroic_str"
                            label="Heroic"
                            tooltip=""
                            type="number"
                            :value="$item->heroic_str"
                            wrapper-class="text-heroic-stat"
                        />
                    </div>
                </div>
                <div class="rounded-lg border border-base-300 p-3 bg-base-100">
                    <div class="text-sm font-medium mb-2">STA</div>
                    <div class="grid grid-cols-2 gap-3">
                        <x-form.input
                            name="asta"
                            label="Base"
                            tooltip="Determines your base health along with your class and race. The higher your
                                stamina, the more health you will have"
                            type="number"
                            min="-128"
                            max="127"
                            :value="$item->asta"
                        />
                        <x-form.input
                            name="heroic_sta"
                            label="Heroic"
                            tooltip=""
                            type="number"
                            :value="$item->heroic_sta"
                            wrapper-class="text-heroic-stat"
                        />
                    </div>
                </div>
                <div class="rounded-lg border border-base-300 p-3 bg-base-100">
                    <div class="text-sm font-medium mb-2">AGI</div>
                    <div class="grid grid-cols-2 gap-3">
                        <x-form.input
                            name="aagi"
                            label="Base"
                            tooltip="Agility Statistic - This determines how often you get hit or missed by an attack
                                and how much damage you take when you get hit. It also affects how quickly you learn
                                defensive skills."
                            type="number"
                            min="-128"
                            max="127"
                            :value="$item->aagi"
                        />
                        <x-form.input
                            name="heroic_agi"
                            label="Heroic"
                            tooltip=""
                            type="number"
                            :value="$item->heroic_agi"
                            wrapper-class="text-heroic-stat"
                        />
                    </div>
                </div>
                <div class="rounded-lg border border-base-300 p-3 bg-base-100">
                    <div class="text-sm font-medium mb-2">DEX</div>
                    <div class="grid grid-cols-2 gap-3">
                        <x-form.input
                            name="adex"
                            label="Base"
                            tooltip="Determines your skills with weapons. Higher dexterity means that if your weapons
                                have effects on them, they will activate (or proc) more frequently. High Dexterity
                                also determines how often you will critical strike with weapons."
                            type="number"
                            min="-128"
                            max="127"
                            :value="$item->adex"
                        />
                        <x-form.input
                            name="heroic_dex"
                            label="Heroic"
                            tooltip=""
                            type="number"
                            :value="$item->heroic_dex"
                            wrapper-class="text-heroic-stat"
                        />
                    </div>
                </div>
                <div class="rounded-lg border border-base-300 p-3 bg-base-100">
                    <div class="text-sm font-medium mb-2">WIS</div>
                    <div class="grid grid-cols-2 gap-3">
                        <x-form.input
                            name="awis"
                            label="Base"
                            tooltip="The same effect as intelligence for the deity-based cc users: Cleric, Shaman,
                                Druid, Paladin, and Ranger. The higher your wisdom, the more mana you get for
                                each level."
                            type="number"
                            min="-128"
                            max="127"
                            :value="$item->awis"
                        />
                        <x-form.input
                            name="heroic_wis"
                            label="Heroic"
                            tooltip=""
                            type="number"
                            :value="$item->heroic_wis"
                            wrapper-class="text-heroic-stat"
                        />
                    </div>
                </div>
                <div class="rounded-lg border border-base-300 p-3 bg-base-100">
                    <div class="text-sm font-medium mb-2">INT</div>
                    <div class="grid grid-cols-2 gap-3">
                        <x-form.input
                            name="aint"
                            label="Base"
                            tooltip="Determines the starting mana for Bards, Enchanters, Magicians, Necromancers,
                                Shadowknights, and Wizards. The higher this stat is for those classes, the more mana
                                they will have."
                            type="number"
                            min="-128"
                            max="127"
                            :value="$item->aint"
                        />
                        <x-form.input
                            name="heroic_int"
                            label="Heroic"
                            tooltip=""
                            type="number"
                            :value="$item->heroic_int"
                            wrapper-class="text-heroic-stat"
                        />
                    </div>
                </div>
                <div class="rounded-lg border border-base-300 p-3 bg-base-100">
                    <div class="text-sm font-medium mb-2">CHA</div>
                    <div class="grid grid-cols-2 gap-3">
                        <x-form.input
                            name="acha"
                            label="Base"
                            tooltip="This stat determines how charismatic you are. It is important for charming and
                                pacify abilities. (enchanter, bard, cleric, ranger). In the raiding game, it will
                                also affect how often Divine Intervention saves you as a tank. However, I highly
                                suggest you ignore this ability at character creation unless you are a bard
                                or enchanter."
                            type="number"
                            min="-128"
                            max="127"
                            :value="$item->acha"
                        />
                        <x-form.input
                            name="heroic_cha"
                            label="Heroic"
                            tooltip=""
                            type="number"
                            :value="$item->heroic_cha"
                            wrapper-class="text-heroic-stat"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="flex items-center justify-between border-b border-base-300 pb-2 mb-4">
                <h2 class="card-title">Resists</h2>
                <span class="text-xs text-base-content/50 uppercase">
                    Elemental Defensive Values
                </span>
            </div>
            <div class="grid grid-cols-4 sm:grid-cols-7 gap-4">
                <div class="rounded-lg border border-base-300 p-3 bg-base-100">
                    <div class="text-sm font-medium mb-2 text-violet-500">Magic</div>
                    <div class="grid grid-cols-2 gap-3">
                        <x-form.input
                            name="mr"
                            label="Base"
                            tooltip="The amount of Magic Resist provided by this item."
                            type="number"
                            min="-128"
                            max="127"
                            :value="$item->mr"
                        />
                        <x-form.input
                            name="heroic_mr"
                            label="Heroic"
                            tooltip=""
                            type="number"
                            :value="$item->heroic_mr"
                            wrapper-class="text-heroic-stat"
                        />
                    </div>
                </div>
                <div class="rounded-lg border border-base-300 p-3 bg-base-100">
                    <div class="text-sm font-medium mb-2 text-red-500">Fire</div>
                    <div class="grid grid-cols-2 gap-3">
                        <x-form.input
                            name="fr"
                            label="Base"
                            tooltip="The amount of Fire Resist provided by this item."
                            type="number"
                            min="-128"
                            max="127"
                            :value="$item->fr"
                        />
                        <x-form.input
                            name="heroic_fr"
                            label="Heroic"
                            tooltip=""
                            type="number"
                            :value="$item->heroic_fr"
                            wrapper-class="text-heroic-stat"
                        />
                    </div>
                </div>
                <div class="rounded-lg border border-base-300 p-3 bg-base-100">
                    <div class="text-sm font-medium mb-2 text-sky-500">Cold</div>
                    <div class="grid grid-cols-2 gap-3">
                        <x-form.input
                            name="cr"
                            label="Base"
                            tooltip="The amount of Cold Resist provided by this item."
                            type="number"
                            min="-128"
                            max="127"
                            :value="$item->cr"
                        />
                        <x-form.input
                            name="heroic_cr"
                            label="Heroic"
                            tooltip=""
                            type="number"
                            :value="$item->heroic_cr"
                            wrapper-class="text-heroic-stat"
                        />
                    </div>
                </div>
                <div class="rounded-lg border border-base-300 p-3 bg-base-100">
                    <div class="text-sm font-medium mb-2 text-lime-600">Disease</div>
                    <div class="grid grid-cols-2 gap-3">
                        <x-form.input
                            name="dr"
                            label="Base"
                            tooltip="The amount of Cold Resist provided by this item."
                            type="number"
                            min="-128"
                            max="127"
                            :value="$item->dr"
                        />
                        <x-form.input
                            name="heroic_dr"
                            label="Heroic"
                            tooltip=""
                            type="number"
                            :value="$item->heroic_dr"
                            wrapper-class="text-heroic-stat"
                        />
                    </div>
                </div>
                <div class="rounded-lg border border-base-300 p-3 bg-base-100">
                    <div class="text-sm font-medium mb-2 text-green-600">Poison</div>
                    <div class="grid grid-cols-2 gap-3">
                        <x-form.input
                            name="pr"
                            label="Base"
                            tooltip="The amount of Poison Resist provided by this item."
                            type="number"
                            min="-128"
                            max="127"
                            :value="$item->pr"
                        />
                        <x-form.input
                            name="heroic_pr"
                            label="Heroic"
                            tooltip=""
                            type="number"
                            :value="$item->heroic_pr"
                            wrapper-class="text-heroic-stat"
                        />
                    </div>
                </div>
                <div class="rounded-lg border border-base-300 p-3 bg-base-100">
                    <div class="text-sm font-medium mb-2 text-rose-600">Corruption</div>
                    <div class="grid grid-cols-2 gap-3">
                        <x-form.input
                            name="svcorruption"
                            label="Base"
                            tooltip="The amount of Corruption Resist provided by this item."
                            type="number"
                            min="-128"
                            max="127"
                            :value="$item->svcorruption"
                        />
                        <x-form.input
                            name="heroic_svcorrup"
                            label="Heroic"
                            tooltip=""
                            type="number"
                            :value="$item->heroic_svcorrup"
                            wrapper-class="text-heroic-stat"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="flex items-center justify-between border-b border-base-300 pb-2 mb-4">
                <h2 class="card-title">Combat &amp; Spell Modifiers</h2>
                <span class="text-xs text-base-content/50 uppercase">
                    Offensive, Defensive, and Regeneration modifiers.
                </span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-4">
                <x-form.input
                    name="accuracy"
                    label="Accuracy"
                    tooltip='Improved chance to hit. This adds to the "hit chance" part of your Attack. 15 accuracy would equal to 1% more landed hits on the mob.'
                    type="number"
                    :value="$item->accuracy"
                />
                <x-form.input
                    name="attack"
                    label="Attack"
                    tooltip="10 attack = around 1% more dps."
                    type="number"
                    :value="$item->attack"
                />
                <x-form.input
                    name="avoidance"
                    label="Avoidance"
                    tooltip="Amount of Avoidance on the Item"
                    type="number"
                    :value="$item->avoidance"
                />
                <x-form.input
                    name="clairvoyance"
                    label="Clairvoyance"
                    tooltip=""
                    type="number"
                    :value="$item->clairvoyance"
                />
                <x-form.input
                    name="combateffects"
                    label="Combat Effects"
                    tooltip=""
                    type="number"
                    :value="$item->combateffects"
                />
                <x-form.input
                    name="dsmitigation"
                    label="Dmg Shield Mit"
                    tooltip=""
                    type="number"
                    :value="$item->dsmitigation"
                />
                <x-form.input
                    name="damageshield"
                    label="Dmg Shield"
                    tooltip=""
                    type="number"
                    :value="$item->damageshield"
                />
                <x-form.input
                    name="dotshielding"
                    label="DoT Shielding"
                    tooltip=""
                    type="number"
                    :value="$item->dotshielding"
                />
                <x-form.input
                    name="enduranceregen"
                    label="End Regen"
                    tooltip="The amount of Endurance Regeneration provided by this item."
                    type="number"
                    :value="$item->enduranceregen"
                />
                <x-form.input
                    name="healamt"
                    label="Heal Amt"
                    tooltip=""
                    type="number"
                    :value="$item->healamt"
                />
                <x-form.input
                    name="regen"
                    label="HP Regen"
                    tooltip="The amount of Hit Point Regeneration provided by this item."
                    type="number"
                    :value="$item->regen"
                />
                <x-form.input
                    name="manaregen"
                    label="Mana Regen"
                    tooltip="The amount of Mana Regeneration provided by this item."
                    type="number"
                    :value="$item->manaregen"
                />
                <x-form.input
                    name="shielding"
                    label="Shielding"
                    tooltip=""
                    type="number"
                    :value="$item->shielding"
                />
                <x-form.input
                    name="spelldmg"
                    label="Spell Dmg"
                    tooltip=""
                    type="number"
                    :value="$item->spelldmg"
                />
                <x-form.input
                    name="strikethrough"
                    label="Strikethrough"
                    tooltip=""
                    type="number"
                    :value="$item->strikethrough"
                />
                <x-form.input
                    name="stunresist"
                    label="Stun Resist"
                    tooltip=""
                    type="number"
                    :value="$item->stunresist"
                />
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="flex items-center justify-between border-b border-base-300 pb-2 mb-4">
                <h2 class="card-title">Weapon &amp; Damage Properties</h2>
                <span class="text-xs text-base-content/50 uppercase">
                    Base, Elemental, and Bane weapon damage, and skill modifiers.
                </span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-4">
                <x-form.input
                    name="damage"
                    label="Damage"
                    tooltip=""
                    type="number"
                    :value="$item->damage"
                />
                <x-form.input
                    name="delay"
                    label="Delay"
                    tooltip="This is the delay for a melee or ranged weapon. It is set in 10ths of a second, so a value of 25 would be a 2.5 second delay."
                    type="number"
                    :value="$item->delay"
                />
                <x-form.input
                    name="haste"
                    label="Haste"
                    tooltip=""
                    type="number"
                    min="0"
                    max="255"
                    :value="$item->haste"
                />
                <x-form.input
                    name="range"
                    label="Range"
                    tooltip="This is the range that an ammo or ranged weapon will use."
                    type="number"
                    :value="$item->range"
                />
                <x-form.select
                    name="extradmgskill"
                    label="Extra Dmg Skill"
                    tooltip="Increases the amount of damage a skill can do. This defines the skill. Values can be 0 - 255."
                    :options="config('everquest.db_skills')"
                    :selected="$item->extradmgskill"
                    keyInOption=true
                />
                <x-form.input
                    name="extradmgamt"
                    label="Extra Dmg Amt"
                    tooltip="Increases the amount of damage a skill can do. This defines how much."
                    type="number"
                    :value="$item->extradmgamt"
                />
                <x-form.input
                    name="backstabdmg"
                    label="Backstab Dmg"
                    tooltip=""
                    type="number"
                    :value="$item->backstabdmg"
                />
                <x-form.select
                    name="banedmgbody"
                    label="Bane Damage Body"
                    tooltip=""
                    :options="config('everquest.db_bodytypes')"
                    :selected="$item->banedmgbody"
                    keyInOption=true
                />
                <x-form.input
                    name="banedmgamt"
                    label="Bane Damage Amount"
                    tooltip="Valid values: 0 -> 255 (-127 -> 127)"
                    type="number"
                    :value="$item->banedmgamt"
                />
                <x-form.select
                    name="banedmgrace"
                    label="Bane Damage Race"
                    tooltip="The Race that the Bane Damage will affect"
                    :options="config('everquest.db_races')"
                    :selected="$item->banedmgrace"
                    keyInOption=true
                />
                <x-form.input
                    name="banedmgraceamt"
                    label="Bane Damage Race Amount"
                    tooltip=""
                    type="number"
                    :value="$item->banedmgraceamt"
                />
                <x-form.select
                    name="elemdmgtype"
                    label="Element Damage Type"
                    tooltip=""
                    :options="config('everquest.db_elements')"
                    :selected="$item->elemdmgtype"
                    keyInOption=true
                />
                <x-form.input
                    name="elemdmgamt"
                    label="Elemental Damage Amount"
                    tooltip=""
                    type="number"
                    :value="$item->elemdmgamt"
                />
                <x-form.select
                    name="bardtype"
                    label="Bard Skill Type"
                    tooltip="The type of instrument modified by bardvalue"
                    :options="[0 => 'None'] + config('everquest.db_bard_skills')"
                    :selected="$item->bardtype"
                    keyInOption=true
                />
                <x-form.input
                    name="bardvalue"
                    label="Bard Value"
                    tooltip="How much the instrument type (bardtype) is modified when equipped"
                    type="number"
                    :value="$item->bardvalue"
                />
                <x-form.select
                    name="skillmodtype"
                    label="Skill Mod Type"
                    tooltip=""
                    :options="config('everquest.db_skills')"
                    :selected="$item->skillmodtype"
                    keyInOption=true
                />
                <x-form.input
                    name="skillmodvalue"
                    label="Skill Mod Value"
                    tooltip=""
                    type="number"
                    :value="$item->skillmodvalue"
                />
                <x-form.input
                    name="skillmodmax"
                    label="Skill Mod Max"
                    tooltip=""
                    type="number"
                    :value="$item->skillmodmax"
                />
            </div>
        </div>
    </div>
</div>
