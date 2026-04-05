@php
    $effects = collect($rank['effects'])
        ->mapWithKeys(fn($e) => [
            $e['slot'] => [
                'slot' => $e['slot'],
                'effectid' => $e['effect_id'],
                'base1' => $e['base1'],
                'base2' => $e['base2'],
                'effectLabel' => $e['effect_id'] !== null
                    ? $e['effect_id'] . ': ' . (config('everquest.spell_effects')[$e['effect_id']] ?? 'Unknown')
                    : null,
            ]
        ]);
@endphp
<div x-data="formTracker">
    <div
        class="mb-2"
        x-data="aaRankEffects"
        x-effect="open && hydrate()"
        data-effects='@json($effects/* , JSON_HEX_APOS|JSON_HEX_QUOT */)'
    >
        <div class="flex items-center justify-between mb-2">
            <h3 class="font-bold text-lg">Effects</h3>

            <button type="button"
                class="btn btn-xs btn-soft btn-success"
                @click="addRow()">
                <x-ui.icon name="add" /> Add Effect
            </button>
        </div>

        <div class="border border-base-content/5 bg-base-100">
            <table class="table table-sm table-zebra w-full">
                <thead class="text-xs uppercase bg-neutral">
                    <tr>
                        <th class="w-[10%]">Slot</th>
                        <th>Effect</th>
                        <th class="w-[30%]">Base1</th>
                        <th class="w-[30%]">Base2</th>
                        <th class="w-[5%]"></th>
                    </tr>
                </thead>

                <tbody>
                    <template x-for="(effect, i) in effectsArray" :key="i">
                        <tr>
                            {{-- SLOT --}}
                            <td>
                                <input name="slot[]"
                                    x-model="effect.slot"
                                    class="input w-full" />
                            </td>

                            {{-- SPA --}}
                            <td>
                                <select
                                    name="effect_id[]"
                                    class="select w-full"
                                    x-data="selectHydrator({
                                        url: '/spells/effects',
                                        valueKey: 'value',
                                        labelKey: 'label',
                                        allowEmpty: true,
                                        noneId: 0,
                                        noneLabel: 'None',
                                        get: () => effect.effectid || 0,
                                        getLabel: () => effect.effectLabel || null
                                    })"
                                    x-on:mousedown="load()"
                                    x-model.number="effect.effectid"
                                >
                                </select>
                            </td>

                            {{-- BASE1 --}}
                            <td>
                                <template x-if="isSpellField(i, 'base1') && !isLimitSpellField(i,'base1')">
                                    <div x-data="ajaxSelect({ searchUrl: '/spells/search', lazy: true })">
                                        <select
                                            :name="`base1[]`"
                                            :data-idx="i"
                                            data-field="base1"
                                            x-ref="select"
                                        ></select>
                                    </div>
                                </template>

                                <template x-if="isItemField(i, 'base1')">
                                    <div x-data="ajaxSelect({ searchUrl: '/items/search', lazy: true })">
                                        <select :name="`base1[]`" :data-idx="i" data-field="base1" x-ref="select"></select>
                                    </div>
                                </template>

                                <template x-if="isLimitSpellField(i, 'base1')">
                                    <div x-data="limitSpellSelect(i, effect.base1, 'base')" class="flex gap-2">
                                        <div x-data="ajaxSelect({ searchUrl: '/spells/search', lazy: true })" class="flex-1">
                                            <select data-limit-spell :name="`base1_display[]`" :data-idx="i" data-field="base1" x-ref="select"></select>
                                        </div>

                                        <select class="select w-20" x-model="mode">
                                            <option value="auto">Auto</option>
                                            <option value="exclude">Exclude</option>
                                        </select>

                                        <input type="hidden" name="base1[]" :value="signedValue" />
                                    </div>
                                </template>

                                <template x-if="!isSpellField(i,'base1') && !isItemField(i,'base1')">
                                    <input type="number"
                                        name="base1[]"
                                        x-model.number="effect.base1"
                                        class="input w-full" />
                                </template>
                            </td>

                            {{-- BASE2 --}}
                            <td>
                                <template x-if="isSpellField(i, 'base2')">
                                    <div x-data="ajaxSelect({ searchUrl: '/spells/search', lazy: true })">
                                        <select :name="`base2[]`" :data-idx="i" data-field="base2" x-ref="select"></select>
                                    </div>
                                </template>

                                <template x-if="isItemField(i, 'base2')">
                                    <div x-data="ajaxSelect({ searchUrl: '/items/search', lazy: true })">
                                        <select :name="`base2[]`" :data-idx="i" data-field="base2" x-ref="select"></select>
                                    </div>
                                </template>

                                <template x-if="!isSpellField(i,'base2') && !isItemField(i,'base2')">
                                    <input type="number"
                                        name="base2[]"
                                        x-model.number="effect.base2"
                                        class="input w-full" />
                                </template>
                            </td>
                            <td class="text-right">
                                <button type="button"
                                    class="btn btn-soft btn-error"
                                    @click="removeRow(i)">
                                    <x-ui.icon name="delete" />
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>
