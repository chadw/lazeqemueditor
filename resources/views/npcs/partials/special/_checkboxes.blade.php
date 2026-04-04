<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 items-center">
    <div class="col-span-full">
        <div class="divider divider-start text-sm text-info uppercase">Simple Abilities</div>
    </div>
    <div data-ability="triple_attack">
        <label class="label cursor-pointer gap-2">
            <input type="checkbox" @change="$store.specialAbilities.toggleByKey('triple_attack', $el)"
                class="checkbox checkbox-sm checked:checkbox-success"
                :checked="$store.specialAbilities.enabled('triple_attack')" />
            <span class="label-text">Triple Attack <span class="text-xs text-neutral-400">(6)</span></span>
        </label>
    </div>
    <div data-ability="quad_attack">
        <label class="label cursor-pointer gap-2">
            <input type="checkbox" @change="$store.specialAbilities.toggleByKey('quad_attack', $el)"
                class="checkbox checkbox-sm checked:checkbox-success"
                :checked="$store.specialAbilities.enabled('quad_attack')" />
            <span class="label-text">Quad Attack <span class="text-xs text-neutral-400">(7)</span></span>
        </label>
    </div>
    <div data-ability="dual_wield">
        <label class="label cursor-pointer gap-2">
            <input type="checkbox" @change="$store.specialAbilities.toggleByKey('dual_wield', $el)"
                class="checkbox checkbox-sm checked:checkbox-success"
                :checked="$store.specialAbilities.enabled('dual_wield')" />
            <span class="label-text">Dual Wield <span class="text-xs text-neutral-400">(8)</span></span>
        </label>
    </div>
    <div data-ability="bane_attack">
        <label class="label cursor-pointer gap-2">
            <input type="checkbox" @change="$store.specialAbilities.toggleByKey('bane_attack', $el)"
                class="checkbox checkbox-sm checked:checkbox-success"
                :checked="$store.specialAbilities.enabled('bane_attack')" />
            <span class="label-text">Bane Attack <span class="text-xs text-neutral-400">(9)</span></span>
        </label>
    </div>
    <div data-ability="magic_attack">
        <label class="label cursor-pointer gap-2">
            <input type="checkbox" @change="$store.specialAbilities.toggleByKey('magic_attack', $el)"
                class="checkbox checkbox-sm checked:checkbox-success"
                :checked="$store.specialAbilities.enabled('magic_attack')" />
            <span class="label-text">Magic Attack <span class="text-xs text-neutral-400">(10)</span></span>
        </label>
    </div>

    <div class="col-span-full">
        <div class="divider divider-start text-sm text-info uppercase">Effect Immunities</div>
    </div>

    <div data-ability="unslowable">
        <label class="label cursor-pointer gap-2">
            <input type="checkbox" @change="$store.specialAbilities.toggleByKey('unslowable', $el)"
                class="checkbox checkbox-sm checked:checkbox-success"
                :checked="$store.specialAbilities.enabled('unslowable')" />
            <span class="label-text">Unslowable <span class="text-xs text-neutral-400">(12)</span></span>
        </label>
    </div>
    <div data-ability="unmezable">
        <label class="label cursor-pointer gap-2">
            <input type="checkbox" @change="$store.specialAbilities.toggleByKey('unmezable', $el)"
                class="checkbox checkbox-sm checked:checkbox-success"
                :checked="$store.specialAbilities.enabled('unmezable')" />
            <span class="label-text">Unmezable <span class="text-xs text-neutral-400">(13)</span></span>
        </label>
    </div>
    <div data-ability="uncharmable">
        <label class="label cursor-pointer gap-2">
            <input type="checkbox" @change="$store.specialAbilities.toggleByKey('uncharmable', $el)"
                class="checkbox checkbox-sm checked:checkbox-success"
                :checked="$store.specialAbilities.enabled('uncharmable')" />
            <span class="label-text">Uncharmable <span class="text-xs text-neutral-400">(14)</span></span>
        </label>
    </div>
    <div data-ability="unstunable">
        <label class="label cursor-pointer gap-2">
            <input type="checkbox" @change="$store.specialAbilities.toggleByKey('unstunable', $el)"
                class="checkbox checkbox-sm checked:checkbox-success"
                :checked="$store.specialAbilities.enabled('unstunable')" />
            <span class="label-text">Unstunable <span class="text-xs text-neutral-400">(15)</span></span>
        </label>
    </div>
    <div data-ability="unsnareable">
        <label class="label cursor-pointer gap-2">
            <input type="checkbox" @change="$store.specialAbilities.toggleByKey('unsnareable', $el)"
                class="checkbox checkbox-sm checked:checkbox-success"
                :checked="$store.specialAbilities.enabled('unsnareable')" />
            <span class="label-text">Unsnareable <span class="text-xs text-neutral-400">(16)</span></span>
        </label>
    </div>
    <div data-ability="unfearable">
        <label class="label cursor-pointer gap-2">
            <input type="checkbox" @change="$store.specialAbilities.toggleByKey('unfearable', $el)"
                class="checkbox checkbox-sm checked:checkbox-success"
                :checked="$store.specialAbilities.enabled('unfearable')" />
            <span class="label-text">Unfearable <span class="text-xs text-neutral-400">(17)</span></span>
        </label>
    </div>
    <div data-ability="unpacifiable">
        <label class="label cursor-pointer gap-2">
            <input type="checkbox" @change="$store.specialAbilities.toggleByKey('unpacifiable', $el)"
                class="checkbox checkbox-sm checked:checkbox-success"
                :checked="$store.specialAbilities.enabled('unpacifiable')" />
            <span class="label-text">Unpacifiable <span class="text-xs text-neutral-400">(31)</span></span>
        </label>
    </div>

    <div class="col-span-full">
        <div class="divider divider-start text-sm text-info uppercase">Immunities</div>
    </div>

    <div data-ability="immune_dispell">
        <label class="label cursor-pointer gap-2">
            <input type="checkbox" @change="$store.specialAbilities.toggleByKey('immune_dispell', $el)"
                class="checkbox checkbox-sm checked:checkbox-success"
                :checked="$store.specialAbilities.enabled('immune_dispell')" />
            <span class="label-text">Immune to Dispel <span
                    class="text-xs text-neutral-400">(18)</span></span>
        </label>
    </div>
    <div data-ability="immune_melee">
        <label class="label cursor-pointer gap-2">
            <input type="checkbox" @change="$store.specialAbilities.toggleByKey('immune_melee', $el)"
                class="checkbox checkbox-sm checked:checkbox-success"
                :checked="$store.specialAbilities.enabled('immune_melee')" />
            <span>Immune to Melee <span class="text-xs text-neutral-400">(19)</span></span>
        </label>
    </div>
    <div data-ability="immune_magic">
        <label class="label cursor-pointer gap-2">
            <input type="checkbox" @change="$store.specialAbilities.toggleByKey('immune_magic', $el)"
                class="checkbox checkbox-sm checked:checkbox-success"
                :checked="$store.specialAbilities.enabled('immune_magic')" />
            <span class="label-text">Immune to Magic <span
                    class="text-xs text-neutral-400">(20)</span></span>
        </label>
    </div>
    <div data-ability="immune_fleeing">
        <label class="label cursor-pointer gap-2">
            <input type="checkbox" @change="$store.specialAbilities.toggleByKey('immune_fleeing', $el)"
                class="checkbox checkbox-sm checked:checkbox-success"
                :checked="$store.specialAbilities.enabled('immune_fleeing')" />
            <span class="label-text">Immune to Fleeing <span
                    class="text-xs text-neutral-400">(21)</span></span>
        </label>
    </div>
    <div data-ability="immune_nonbane_melee">
        <label class="label cursor-pointer gap-2">
            <input type="checkbox"
                @change="$store.specialAbilities.toggleByKey('immune_nonbane_melee', $el)"
                class="checkbox checkbox-sm checked:checkbox-success"
                :checked="$store.specialAbilities.enabled('immune_nonbane_melee')" />
            <span class="label-text">Immune to non-Bane Melee <span
                    class="text-xs text-neutral-400">(22)</span></span>
        </label>
    </div>
    <div data-ability="immune_nonmagical_melee">
        <label class="label cursor-pointer gap-2">
            <input type="checkbox"
                @change="$store.specialAbilities.toggleByKey('immune_nonmagical_melee', $el)"
                class="checkbox checkbox-sm checked:checkbox-success"
                :checked="$store.specialAbilities.enabled('immune_nonmagical_melee')" />
            <span class="label-text">Immune to non-Magical Melee <span
                    class="text-xs text-neutral-400">(23)</span></span>
        </label>
    </div>
    <div data-ability="immune_aggro">
        <label class="label cursor-pointer gap-2">
            <input type="checkbox" @change="$store.specialAbilities.toggleByKey('immune_aggro', $el)"
                class="checkbox checkbox-sm checked:checkbox-success"
                :checked="$store.specialAbilities.enabled('immune_aggro')" />
            <span class="label-text">Immune to Aggro <span
                    class="text-xs text-neutral-400">(25)</span></span>
        </label>
    </div>
    <div data-ability="immune_taunt">
        <label class="label cursor-pointer gap-2">
            <input type="checkbox" @change="$store.specialAbilities.toggleByKey('immune_taunt', $el)"
                class="checkbox checkbox-sm checked:checkbox-success"
                :checked="$store.specialAbilities.enabled('immune_taunt')" />
            <span class="label-text">Immune to Taunt <span
                    class="text-xs text-neutral-400">(28)</span></span>
        </label>
    </div>
    <div data-ability="immune_ranged_attacks">
        <label class="label cursor-pointer gap-2">
            <input type="checkbox"
                @change="$store.specialAbilities.toggleByKey('immune_ranged_attacks', $el)"
                class="checkbox checkbox-sm checked:checkbox-success"
                :checked="$store.specialAbilities.enabled('immune_ranged_attacks')" />
            <span class="label-text">Immune to Ranged Attacks <span
                    class="text-xs text-neutral-400">(46)</span></span>
        </label>
    </div>
    <div data-ability="immune_client_damage">
        <label class="label cursor-pointer gap-2">
            <input type="checkbox"
                @change="$store.specialAbilities.toggleByKey('immune_client_damage', $el)"
                class="checkbox checkbox-sm checked:checkbox-success"
                :checked="$store.specialAbilities.enabled('immune_client_damage')" />
            <span class="label-text">Immune to Client Damage <span
                    class="text-xs text-neutral-400">(47)</span></span>
        </label>
    </div>
    <div data-ability="immune_npc_damage">
        <label class="label cursor-pointer gap-2">
            <input type="checkbox" @change="$store.specialAbilities.toggleByKey('immune_npc_damage', $el)"
                class="checkbox checkbox-sm checked:checkbox-success"
                :checked="$store.specialAbilities.enabled('immune_npc_damage')" />
            <span class="label-text">Immune to NPC Damage <span
                    class="text-xs text-neutral-400">(48)</span></span>
        </label>
    </div>
    <div data-ability="immune_client_aggro">
        <label class="label cursor-pointer gap-2">
            <input type="checkbox"
                @change="$store.specialAbilities.toggleByKey('immune_client_aggro', $el)"
                class="checkbox checkbox-sm checked:checkbox-success"
                :checked="$store.specialAbilities.enabled('immune_client_aggro')" />
            <span class="label-text">Immune to Client Aggro <span
                    class="text-xs text-neutral-400">(49)</span></span>
        </label>
    </div>
    <div data-ability="immune_npc_aggro">
        <label class="label cursor-pointer gap-2">
            <input type="checkbox" @change="$store.specialAbilities.toggleByKey('immune_npc_aggro', $el)"
                class="checkbox checkbox-sm checked:checkbox-success"
                :checked="$store.specialAbilities.enabled('immune_npc_aggro')" />
            <span class="label-text">Immune to NPC Aggro <span
                    class="text-xs text-neutral-400">(50)</span></span>
        </label>
    </div>
    <div data-ability="immune_fades">
        <label class="label cursor-pointer gap-2">
            <input type="checkbox" @change="$store.specialAbilities.toggleByKey('immune_fades', $el)"
                class="checkbox checkbox-sm checked:checkbox-success"
                :checked="$store.specialAbilities.enabled('immune_fades')" />
            <span class="label-text">Immune to Memory Fades <span
                    class="text-xs text-neutral-400">(52)</span></span>
        </label>
    </div>
    <div data-ability="immune_open">
        <label class="label cursor-pointer gap-2">
            <input type="checkbox" @change="$store.specialAbilities.toggleByKey('immune_open', $el)"
                class="checkbox checkbox-sm checked:checkbox-success"
                :checked="$store.specialAbilities.enabled('immune_open')" />
            <span class="label-text">Immune to Open <span
                    class="text-xs text-neutral-400">(53)</span></span>
        </label>
    </div>
    <div data-ability="immune_assassinate">
        <label class="label cursor-pointer gap-2">
            <input type="checkbox"
                @change="$store.specialAbilities.toggleByKey('immune_assassinate', $el)"
                class="checkbox checkbox-sm checked:checkbox-success"
                :checked="$store.specialAbilities.enabled('immune_assassinate')" />
            <span class="label-text">Immune to Assassinate <span
                    class="text-xs text-neutral-400">(54)</span></span>
        </label>
    </div>
    <div data-ability="immune_headshot">
        <label class="label cursor-pointer gap-2">
            <input type="checkbox" @change="$store.specialAbilities.toggleByKey('immune_headshot', $el)"
                class="checkbox checkbox-sm checked:checkbox-success"
                :checked="$store.specialAbilities.enabled('immune_headshot')" />
            <span class="label-text">Immune to Headshot <span
                    class="text-xs text-neutral-400">(55)</span></span>
        </label>
    </div>
    <div data-ability="immune_bot_aggro">
        <label class="label cursor-pointer gap-2">
            <input type="checkbox" @change="$store.specialAbilities.toggleByKey('immune_bot_aggro', $el)"
                class="checkbox checkbox-sm checked:checkbox-success"
                :checked="$store.specialAbilities.enabled('immune_bot_aggro')" />
            <span class="label-text">Immune to Bot Aggro <span
                    class="text-xs text-neutral-400">(56)</span></span>
        </label>
    </div>
    <div data-ability="immune_bot_damage">
        <label class="label cursor-pointer gap-2">
            <input type="checkbox" @change="$store.specialAbilities.toggleByKey('immune_bot_damage', $el)"
                class="checkbox checkbox-sm checked:checkbox-success"
                :checked="$store.specialAbilities.enabled('immune_bot_damage')" />
            <span class="label-text">Immune to Bot Damage <span
                    class="text-xs text-neutral-400">(57)</span></span>
        </label>
    </div>

    <div class="col-span-full">
        <div class="divider divider-start text-sm text-info uppercase">Misc</div>
    </div>

    <div data-ability="will_not_aggro">
        <label class="label cursor-pointer gap-2">
            <input type="checkbox" @change="$store.specialAbilities.toggleByKey('will_not_aggro', $el)"
                class="checkbox checkbox-sm checked:checkbox-success"
                :checked="$store.specialAbilities.enabled('will_not_aggro')" />
            <span class="label-text">Will Not Aggro <span class="text-xs text-neutral-400">(24)</span></span>
        </label>
    </div>
    <div data-ability="resist_ranged_spells">
        <label class="label cursor-pointer gap-2">
            <input type="checkbox"
                @change="$store.specialAbilities.toggleByKey('resist_ranged_spells', $el)"
                class="checkbox checkbox-sm checked:checkbox-success"
                :checked="$store.specialAbilities.enabled('resist_ranged_spells')" />
            <span class="label-text">Resist Ranged Spells <span class="text-xs text-neutral-400">(26)</span></span>
        </label>
    </div>
    <div data-ability="see_through_feign_death">
        <label class="label cursor-pointer gap-2">
            <input type="checkbox"
                @change="$store.specialAbilities.toggleByKey('see_through_feign_death', $el)"
                class="checkbox checkbox-sm checked:checkbox-success"
                :checked="$store.specialAbilities.enabled('see_through_feign_death')" />
            <span class="label-text">See through Feign Death <span class="text-xs text-neutral-400">(27)</span></span>
        </label>
    </div>
    <div data-ability="no_buff_to_friends">
        <label class="label cursor-pointer gap-2">
            <input type="checkbox"
                @change="$store.specialAbilities.toggleByKey('no_buff_to_friends', $el)"
                class="checkbox checkbox-sm checked:checkbox-success"
                :checked="$store.specialAbilities.enabled('no_buff_to_friends')" />
            <span class="label-text">Does NOT buff/heal friends <span class="text-xs text-neutral-400">(30)</span></span>
        </label>
    </div>
    <div data-ability="destructible_object">
        <label class="label cursor-pointer gap-2">
            <input type="checkbox"
                @change="$store.specialAbilities.toggleByKey('destructible_object', $el)"
                class="checkbox checkbox-sm checked:checkbox-success"
                :checked="$store.specialAbilities.enabled('destructible_object')" />
            <span class="label-text">Destructible Object <span class="text-xs text-neutral-400">(34)</span></span>
        </label>
    </div>
    <div data-ability="no_harm_from_players">
        <label class="label cursor-pointer gap-2">
            <input type="checkbox"
                @change="$store.specialAbilities.toggleByKey('no_harm_from_players', $el)"
                class="checkbox checkbox-sm checked:checkbox-success"
                :checked="$store.specialAbilities.enabled('no_harm_from_players')" />
            <span class="label-text">No Harm from Players <span class="text-xs text-neutral-400">(35)</span></span>
        </label>
    </div>
    <div data-ability="always_flee">
        <label class="label cursor-pointer gap-2">
            <input type="checkbox" @change="$store.specialAbilities.toggleByKey('always_flee', $el)"
                class="checkbox checkbox-sm checked:checkbox-success"
                :checked="$store.specialAbilities.enabled('always_flee')" />
            <span class="label-text">Always Flee <span class="text-xs text-neutral-400">(36)</span></span>
        </label>
    </div>
    <div data-ability="allow_beneficial">
        <label class="label cursor-pointer gap-2">
            <input type="checkbox" @change="$store.specialAbilities.toggleByKey('allow_beneficial', $el)"
                class="checkbox checkbox-sm checked:checkbox-success"
                :checked="$store.specialAbilities.enabled('allow_beneficial')" />
            <span class="label-text">Allow Beneficial <span class="text-xs text-neutral-400">(38)</span></span>
        </label>
    </div>
    <div data-ability="disable_melee">
        <label class="label cursor-pointer gap-2">
            <input type="checkbox" @change="$store.specialAbilities.toggleByKey('disable_melee', $el)"
                class="checkbox checkbox-sm checked:checkbox-success"
                :checked="$store.specialAbilities.enabled('disable_melee')" />
            <span class="label-text">Disable Melee <span class="text-xs text-neutral-400">(39)</span></span>
        </label>
    </div>
    <div data-ability="ignore_root_aggro">
        <label class="label cursor-pointer gap-2">
            <input type="checkbox" @change="$store.specialAbilities.toggleByKey('ignore_root_aggro', $el)"
                class="checkbox checkbox-sm checked:checkbox-success"
                :checked="$store.specialAbilities.enabled('ignore_root_aggro')" />
            <span class="label-text">Ignore Root Aggro <span class="text-xs text-neutral-400">(42)</span></span>
        </label>
    </div>
    <div data-ability="proximity_aggro">
        <label class="label cursor-pointer gap-2">
            <input type="checkbox" @change="$store.specialAbilities.toggleByKey('proximity_aggro', $el)"
                class="checkbox checkbox-sm checked:checkbox-success"
                :checked="$store.specialAbilities.enabled('proximity_aggro')" />
            <span class="label-text">Proximity Aggro <span class="text-xs text-neutral-400">(45)</span></span>
        </label>
    </div>
</div>
