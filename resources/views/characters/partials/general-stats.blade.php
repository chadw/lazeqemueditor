<div class="bg-base-200 p-4 rounded">
    @if ($character->stats)
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 rounded bg-base-100">
                        <div class="text-xs text-muted">HP</div>
                        <div class="text-2xl font-bold text-primary">
                            {{ optional($character->stats)->hp ?? 0 }}</div>
                        <div class="text-xs text-muted mt-1">AC: {{ optional($character->stats)->ac ?? 0 }} ·
                            ATK: {{ optional($character->stats)->attack ?? 0 }}</div>
                    </div>

                    <div class="p-4 rounded bg-base-100">
                        <div class="text-xs text-muted">Mana / Endurance</div>
                        <div class="text-2xl font-bold">
                            {{ optional($character->stats)->mana > 0 ? optional($character->stats)->mana : 0 }}
                            / {{ optional($character->stats)->endurance ?? 0 }}</div>
                        <div class="text-xs text-muted mt-1">Haste:
                            {{ optional($character->stats)->haste ?? 0 }}%</div>
                    </div>
                </div>

                <div class="divider my-4"></div>

                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    <div class="p-3 bg-base-100 rounded">
                        <div class="text-xs text-muted">STR</div>
                        <div class="font-medium">{{ optional($character->stats)->strength ?? 0 }}
                            <span class="text-xs text-heroic-stat">
                                (+{{ optional($character->stats)->heroic_strength ?? 0 }})
                            </span>
                        </div>
                    </div>
                    <div class="p-3 bg-base-100 rounded">
                        <div class="text-xs text-muted">STA</div>
                        <div class="font-medium">{{ optional($character->stats)->stamina ?? 0 }}
                            <span class="text-xs text-heroic-stat">
                                (+{{ optional($character->stats)->heroic_stamina ?? 0 }})
                            </span>
                        </div>
                    </div>
                    <div class="p-3 bg-base-100 rounded">
                        <div class="text-xs text-muted">AGI</div>
                        <div class="font-medium">{{ optional($character->stats)->agility ?? 0 }}
                            <span class="text-xs text-heroic-stat">
                                (+{{ optional($character->stats)->heroic_agility ?? 0 }})
                            </span>
                        </div>
                    </div>
                    <div class="p-3 bg-base-100 rounded">
                        <div class="text-xs text-muted">DEX</div>
                        <div class="font-medium">{{ optional($character->stats)->dexterity ?? 0 }}
                            <span class="text-xs text-heroic-stat">
                                (+{{ optional($character->stats)->heroic_dexterity ?? 0 }})
                            </span>
                        </div>
                    </div>
                    <div class="p-3 bg-base-100 rounded">
                        <div class="text-xs text-muted">INT</div>
                        <div class="font-medium">{{ optional($character->stats)->intelligence ?? 0 }}
                            <span class="text-xs text-heroic-stat">
                                (+{{ optional($character->stats)->heroic_intelligence ?? 0 }})
                            </span>
                        </div>
                    </div>
                    <div class="p-3 bg-base-100 rounded">
                        <div class="text-xs text-muted">WIS</div>
                        <div class="font-medium">{{ optional($character->stats)->wisdom ?? 0 }}
                            <span class="text-xs text-heroic-stat">
                                (+{{ optional($character->stats)->heroic_wisdom ?? 0 }})
                            </span>
                        </div>
                    </div>
                    <div class="p-3 bg-base-100 rounded">
                        <div class="text-xs text-muted">CHA</div>
                        <div class="font-medium">{{ optional($character->stats)->charisma ?? 0 }}
                            <span class="text-xs text-heroic-stat">
                                (+{{ optional($character->stats)->heroic_charisma ?? 0 }})
                            </span>
                        </div>
                    </div>
                </div>

                <div class="divider my-4"></div>

                <div>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        <div class="p-3 bg-base-100 rounded">
                            <div class="text-xs text-muted">Magic</div>
                            <div class="font-medium">{{ optional($character->stats)->magic_resist ?? 0 }}
                                <span class="text-xs text-heroic-stat">
                                    (+{{ optional($character->stats)->heroic_magic_resist ?? 0 }})
                                </span>
                            </div>
                        </div>
                        <div class="p-3 bg-base-100 rounded">
                            <div class="text-xs text-muted">Fire</div>
                            <div class="font-medium">{{ optional($character->stats)->fire_resist ?? 0 }}
                                <span
                                    class="text-xs text-heroic-stat">(+{{ optional($character->stats)->heroic_fire_resist ?? 0 }})</span>
                            </div>
                        </div>
                        <div class="p-3 bg-base-100 rounded">
                            <div class="text-xs text-muted">Cold</div>
                            <div class="font-medium">{{ optional($character->stats)->cold_resist ?? 0 }}
                                <span
                                    class="text-xs text-heroic-stat">(+{{ optional($character->stats)->heroic_cold_resist ?? 0 }})</span>
                            </div>
                        </div>
                        <div class="p-3 bg-base-100 rounded">
                            <div class="text-xs text-muted">Poison</div>
                            <div class="font-medium">{{ optional($character->stats)->poison_resist ?? 0 }}
                                <span
                                    class="text-xs text-heroic-stat">(+{{ optional($character->stats)->heroic_poison_resist ?? 0 }})</span>
                            </div>
                        </div>
                        <div class="p-3 bg-base-100 rounded">
                            <div class="text-xs text-muted">Disease</div>
                            <div class="font-medium">{{ optional($character->stats)->disease_resist ?? 0 }}
                                <span
                                    class="text-xs text-heroic-stat">(+{{ optional($character->stats)->heroic_disease_resist ?? 0 }})</span>
                            </div>
                        </div>
                        <div class="p-3 bg-base-100 rounded">
                            <div class="text-xs text-muted">Corruption</div>
                            <div class="font-medium">
                                {{ optional($character->stats)->corruption_resist ?? 0 }} <span
                                    class="text-xs text-heroic-stat">(+{{ optional($character->stats)->heroic_corruption_resist ?? 0 }})</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <div class="bg-base-100 p-4 rounded">
                    <div class="grid grid-cols-1 gap-3">
                        <div class="flex items-center gap-3">
                            <div class="text-sm text-muted">HP Regen</div>
                            <div class="flex-1 border-b border-dotted border-base-content/20"></div>
                            <div class="font-medium">{{ optional($character->stats)->hp_regen ?? 0 }}</div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="text-sm text-muted">Mana Regen</div>
                            <div class="flex-1 border-b border-dotted border-base-content/20"></div>
                            <div class="font-medium">{{ optional($character->stats)->mana_regen ?? 0 }}</div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="text-sm text-muted">End Regen</div>
                            <div class="flex-1 border-b border-dotted border-base-content/20"></div>
                            <div class="font-medium">{{ optional($character->stats)->endurance_regen ?? 0 }}
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="text-sm text-muted">Spell Shield</div>
                            <div class="flex-1 border-b border-dotted border-base-content/20"></div>
                            <div class="font-medium">{{ optional($character->stats)->spell_shielding ?? 0 }}
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="text-sm text-muted">Shielding</div>
                            <div class="flex-1 border-b border-dotted border-base-content/20"></div>
                            <div class="font-medium">{{ optional($character->stats)->shielding ?? 0 }}</div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="text-sm text-muted">Dmg Shield</div>
                            <div class="flex-1 border-b border-dotted border-base-content/20"></div>
                            <div class="font-medium">{{ optional($character->stats)->damage_shield ?? 0 }}
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="text-sm text-muted">DoT Shield</div>
                            <div class="flex-1 border-b border-dotted border-base-content/20"></div>
                            <div class="font-medium">{{ optional($character->stats)->dot_shielding ?? 0 }}
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="text-sm text-muted">Dmg Shld Mit</div>
                            <div class="flex-1 border-b border-dotted border-base-content/20"></div>
                            <div class="font-medium">
                                {{ optional($character->stats)->damage_shield_mitigation ?? 0 }}</div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="text-sm text-muted">Avoidance</div>
                            <div class="flex-1 border-b border-dotted border-base-content/20"></div>
                            <div class="font-medium">{{ optional($character->stats)->avoidance ?? 0 }}</div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="text-sm text-muted">Accuracy</div>
                            <div class="flex-1 border-b border-dotted border-base-content/20"></div>
                            <div class="font-medium">{{ optional($character->stats)->accuracy ?? 0 }}</div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="text-sm text-muted">Stun Resist</div>
                            <div class="flex-1 border-b border-dotted border-base-content/20"></div>
                            <div class="font-medium">{{ optional($character->stats)->stun_resist ?? 0 }}</div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="text-sm text-muted">Strike Thr</div>
                            <div class="flex-1 border-b border-dotted border-base-content/20"></div>
                            <div class="font-medium">{{ optional($character->stats)->strikethrough ?? 0 }}
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="text-sm text-muted">Spell Dmg</div>
                            <div class="flex-1 border-b border-dotted border-base-content/20"></div>
                            <div class="font-medium">{{ optional($character->stats)->spell_damage ?? 0 }}
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="text-sm text-muted">Heal Amt</div>
                            <div class="flex-1 border-b border-dotted border-base-content/20"></div>
                            <div class="font-medium">{{ optional($character->stats)->heal_amount ?? 0 }}</div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="text-sm text-muted">Combat Eff</div>
                            <div class="flex-1 border-b border-dotted border-base-content/20"></div>
                            <div class="font-medium">{{ optional($character->stats)->combat_effects ?? 0 }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <p class="text-muted">No stats available.</p>
    @endif
</div>
