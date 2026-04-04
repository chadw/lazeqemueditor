<?php

namespace App\Support\Aa;

use App\Models\Spell;
use App\Models\Item;

class AaRankEffectPresenter
{
    public static function present($effect)
    {
        $spaDefs = config('eqemu_spa_defs.spa_defs');
        $spa = $spaDefs[$effect->effect_id] ?? null;

        if (!$spa) {
            return [
                'name' => "Unknown SPA ({$effect->effect_id})",
                'details' => [],
            ];
        }

        $details = [];

        // Base 1
        if (str_contains($spa['base'], 'spellid')) {
            $details['Spell'] = Spell::find($effect->base1)?->name ?? "Spell #{$effect->base1}";
        } elseif (str_contains($spa['base'], 'item')) {
            $details['Item'] = Item::find($effect->base1)?->name ?? "Item #{$effect->base1}";
        } else {
            $details[$spa['base']] = $effect->base1;
        }

        // Base 2
        if (!empty($spa['limit'])) {
            if (str_contains($spa['limit'], 'spellid')) {
                $details['Limit Spell'] =
                    Spell::find($effect->base2)?->name ?? "Spell #{$effect->base2}";
            } elseif (str_contains($spa['limit'], 'item')) {
                $details['Limit Item'] =
                    Item::find($effect->base2)?->name ?? "Item #{$effect->base2}";
            } else {
                $details[$spa['limit']] = $effect->base2;
            }
        }

        return [
            'name' => $spa['effectName'],
            'description' => $spa['description'],
            'details' => $details,
        ];
    }
}
