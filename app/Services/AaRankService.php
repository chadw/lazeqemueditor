<?php

namespace App\Services;

use App\Models\AaRank;
use App\Models\Spell;
use App\Models\Item;

class AaRankService
{
    protected array $spaDefs;

    public function __construct()
    {
        $this->spaDefs = (array) config('eqemu_spa_defs');
    }

    /**
     * Present a full rank for display
     *
     * @param  mixed $rank
     * @return array
     */
    public function presentRank(AaRank $rank): array
    {
        return array_merge(
            $rank->toArray(),
            [
                'effects' => $this->presentEffects($rank),
                'prereqs' => $rank->prereqs,
            ]
        );
    }

    /**
     * Present all effects for a rank
     *
     * @param  mixed $rank
     * @return array
     */
    public function presentEffects(AaRank $rank): array
    {
        $effects = $rank->effects;
        $spellIds = [];
        $itemIds  = [];

        foreach ($effects as $effect) {
            foreach (['base1', 'base2'] as $baseField) {
                foreach (['base', 'limit'] as $labelKey) {
                    $spaLabel = $this->spaDefs[$effect->effect_id][$labelKey] ?? null;
                    $value = $effect->{$baseField};
                    if (!$spaLabel || $value === null || $value === 0) continue;

                    $abs = abs($value);

                    $labelLower = strtolower($spaLabel);
                    if (str_contains($labelLower, 'spellid') || str_contains($labelLower, 'spell id')) {
                        $spellIds[] = $abs;
                    }
                    if (str_contains($labelLower, 'item')) {
                        $itemIds[] = $abs;
                    }
                }
            }
        }

        $spellMap = Spell::whereIn('id', array_unique($spellIds))
            ->select('id', 'name', 'new_icon')
            ->get()
            ->keyBy('id');

        $itemMap = Item::whereIn('id', array_unique($itemIds))
            ->select('id', 'Name', 'icon')
            ->get()
            ->keyBy('id');

        return $effects->map(function ($effect) use ($spellMap, $itemMap) {
            return $this->presentEffect($effect, $spellMap, $itemMap);
        })->toArray();
    }

    /**
     * Present a single effect
     *
     * @param  mixed $effect
     * @param  mixed $spellMap
     * @param  mixed $itemMap
     * @return array
     */
    public function presentEffect($effect, $spellMap, $itemMap): array
    {
        $spa = $this->spaDefs[$effect->effect_id] ?? null;

        if (!$spa) {
            return [
                'rank_id'      => $effect->rank_id,
                'slot'         => $effect->slot,
                'effect_id'    => $effect->effect_id,
                'base1'        => $effect->base1,
                'base2'        => $effect->base2,
                //'name'         => "Unknown SPA ({$effect->effect_id})",
                //'description'  => null,
                //'category'     => null,
                'base1_detail' => null,
                'base2_detail' => null,
            ];
        }

        return [
            'rank_id'      => $effect->rank_id,
            'slot'         => $effect->slot,
            'effect_id'    => $effect->effect_id,
            'base1'        => $effect->base1,
            'base2'        => $effect->base2,
            //'name'         => $spa['effectName'],
            //'description'  => $spa['description'] ?? null,
            //'category'     => $spa['category'] ?? null,
            'base1_detail' => $this->resolveBase($spa['base'] ?? null, $effect->base1, $spellMap, $itemMap),
            'base2_detail' => $this->resolveBase($spa['limit'] ?? null, $effect->base2, $spellMap, $itemMap),
        ];
    }

    /**
     * Resolve a single SPA base value to displayable content.
     * Returns either null or an array with id, name, new_icon.
     *
     * @param  mixed $label
     * @param  mixed $value
     * @param  mixed $spellMap
     * @param  mixed $itemMap
     * @return array
     */
    protected function resolveBase(?string $label, ?int $value, $spellMap, $itemMap): array|string|int|null
    {
        if (!$label || $value === null || $value === 0) {
            return null;
        }

        $absValue = abs($value);
        $labelLower = strtolower($label);

        if (str_contains($labelLower, 'spellid') || str_contains($labelLower, 'spell id')) {
            return $spellMap[$absValue]?->toArray()
                ?? ['id' => $absValue, 'name' => "Spell #{$absValue}", 'new_icon' => null];
        }

        if (str_contains($labelLower, 'item')) {
            return $itemMap[$absValue]?->toArray()
                ?? ['id' => $absValue, 'name' => "Item #{$absValue}", 'icon' => null];
        }

        return $value;
    }
}
