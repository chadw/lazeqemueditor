<?php

namespace App\Services;

use Illuminate\Support\Collection;

class CharacterStats
{
    public static function calculate(Collection $items): array
    {
        $stats = [
            'hp', 'mana', 'endur', 'ac', 'regen', 'manaregen', 'enduranceregen',
            'spellshield', 'shielding', 'damageshield', 'dotshielding', 'dsmitigation',
            'avoidance', 'accuracy', 'stunresist', 'strikethrough', 'healamt', 'spelldmg',
            'combateffects',
        ];
        $totals = array_fill_keys($stats, 0);

        foreach ($items as $inventory) {
            $allItems = [$inventory->item];

            for ($i = 1; $i <= 6; $i++) {
                $aug = $inventory->{"aug$i"} ?? null;
                if ($aug) {
                    $allItems[] = $aug;
                }
            }

            foreach ($allItems as $item) {
                foreach ($stats as $stat) {
                    $totals[$stat] += $item->$stat ?? 0;
                }
            }
        }

        return $totals;
    }

    public static function calculateZZ(Collection $items): array
    {
        //$items = collect($items);
        //calculate base hp - https://github.com/EQEmu/Server/blob/e948a6815c0312164d041b81fba905abe8e6fa4b/zone/client_mods.cpp#L482
        //calculate max hp - https://github.com/EQEmu/Server/blob/e948a6815c0312164d041b81fba905abe8e6fa4b/zone/client_mods.cpp#L317
        return [
            'hp' => $items->sum(fn($inventory) => $inventory->item->hp ?? 0),
            'mana' => $items->sum(fn($inventory) => $inventory->item->mana ?? 0),
            'end' => $items->sum(fn($inventory) => $inventory->item->endur ?? 0),
            'ac' => $items->sum(fn($inventory) => $inventory->item->ac ?? 0),

            'str' => $items->sum(fn($inventory) => $inventory->item->astr ?? 0),
            'sta' => $items->sum(fn($inventory) => $inventory->item->asta ?? 0),
            'agi' => $items->sum(fn($inventory) => $inventory->item->aagi ?? 0),
            'dex' => $items->sum(fn($inventory) => $inventory->item->adex ?? 0),
            'wis' => $items->sum(fn($inventory) => $inventory->item->awis ?? 0),
            'int' => $items->sum(fn($inventory) => $inventory->item->aint ?? 0),
            'cha' => $items->sum(fn($inventory) => $inventory->item->acha ?? 0),

            'hstr' => $items->sum(fn($inventory) => $inventory->item->heroic_str ?? 0),
            'hsta' => $items->sum(fn($inventory) => $inventory->item->heroic_sta ?? 0),
            'hagi' => $items->sum(fn($inventory) => $inventory->item->heroic_agi ?? 0),
            'hdex' => $items->sum(fn($inventory) => $inventory->item->heroic_dex ?? 0),
            'hwis' => $items->sum(fn($inventory) => $inventory->item->heroic_wis ?? 0),
            'hint' => $items->sum(fn($inventory) => $inventory->item->heroic_int ?? 0),
            'hcha' => $items->sum(fn($inventory) => $inventory->item->heroic_cha ?? 0),

            'mr' => $items->sum(fn($inventory) => $inventory->item->mr ?? 0),
            'fr' => $items->sum(fn($inventory) => $inventory->item->fr ?? 0),
            'cr' => $items->sum(fn($inventory) => $inventory->item->cr ?? 0),
            'pr' => $items->sum(fn($inventory) => $inventory->item->pr ?? 0),
            'dr' => $items->sum(fn($inventory) => $inventory->item->dr ?? 0),
            'corrupt' => $items->sum(fn($inventory) => $inventory->item->svcorruption ?? 0),

            'hp_regen' => $items->sum(fn($inventory) => $inventory->item->regen ?? 0),
            'mana_regen' => $items->sum(fn($inventory) => $inventory->item->manaregen ?? 0),
            'end_regen' => $items->sum(fn($inventory) => $inventory->item->enduranceregen ?? 0),
            'spell_shielding' => $items->sum(fn($inventory) => $inventory->item->spellshield ?? 0),
            'shielding' => $items->sum(fn($inventory) => $inventory->item->shielding ?? 0),
            'dmg_shielding' => $items->sum(fn($inventory) => $inventory->item->damageshield ?? 0),
            'dot_shielding' => $items->sum(fn($inventory) => $inventory->item->dotshielding ?? 0),
        ];
    }
}
