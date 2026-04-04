@php
    $initialActive = array_values(
        array_filter(range(1, 12), function ($i) use ($spell) {
            return $spell->{'effectid' . $i} !== 254;
        }),
    );

    $spellValues = [];
    for ($i = 1; $i <= 12; $i++) {
        $spellValues[$i] = [
            'effectid' => $spell->{'effectid' . $i},
            'effect_base_value' => $spell->{'effect_base_value' . $i},
            'effect_limit_value' => $spell->{'effect_limit_value' . $i},
            'max' => $spell->{'max' . $i},
            'formula' => $spell->{'formula' . $i},
        ];
    }
@endphp

<div
    x-data="spellEffects"
    x-cloak
    @formula-picked.window="spellValues[$event.detail.index].formula = $event.detail.value"
    class="space-y-4"
    data-initial-active='@json($initialActive)'
    data-spell-values='@json($spellValues)'
    data-teleport='@json($spell->teleport_zone)'
    data-db-races='@json(config('everquest.db_races'))'
    data-spa-defs='@json(config('eqemu_spa_defs'), JSON_FORCE_OBJECT | JSON_HEX_APOS
    | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP)'
>
    <div class="flex flex-wrap gap-2 mb-4">
        @for ($i = 1; $i <= 12; $i++)
            <button type="button" @click="toggleEffect({{ $i }})"
                :class="activeEffects.includes({{ $i }}) ? 'btn-success' : ''"
                class="btn btn-soft btn-sm">
                {{ $i }}
            </button>
        @endfor
    </div>
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-[30px_1fr_1fr_1fr_100px_140px] gap-1 p-2 rounded font-semibold border-b border-base-content/10">
                <div>#</div>
                <div>Effect</div>
                <div>Base</div>
                <div>Limit</div>
                <div>Max</div>
                <div>Formula</div>
            </div>
            <template x-for="i in sortedActive()" :key="i">
                <div :class="`grid grid-cols-[30px_1fr_1fr_1fr_100px_140px] gap-1 items-center p-2 mb-0 ${i % 2 === 0 ? 'bg-base-300' : 'bg-base-100'}`"
                    @mouseenter="(function(){ try{ const spaId = Number(spellValues[i]?.effectid)||0; const def = (spaDefs || {})[spaId] || null; window.dispatchEvent(new CustomEvent('effect-desc', { detail: { id: spaId, def: def } })); }catch(e){} })()"
                    @mouseleave="(function(){ try{ window.dispatchEvent(new CustomEvent('effect-desc-hide')); }catch(e){} })()"
                >
                    <div x-text="i"></div>
                    <div>
                        <select :name="`effectid${i}`" x-model.number="spellValues[i].effectid" @focus="setSelected(i)"
                            @click="setSelected(i)" @change="onEffectIdChanged(i)" class="select w-full">
                            @foreach (config('everquest.spell_effects') as $key => $label)
                                <option value="{{ $key }}">{{ $key }}: {{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- base value --}}
                    <div>
                        <div
                            x-show="isItemField(i, 'base')"
                            x-transition
                        >
                            <div
                                x-data="ajaxSelect({
                                    searchUrl: '/items/search',
                                    prefillPath: ''
                                })"
                                x-init="init()"
                                :key="`item-base-${i}`"
                            >
                                <select
                                    x-ref="select"
                                    :name="`effect_base_value${i}`"
                                    class="w-full"
                                ></select>
                            </div>
                        </div>
                        <template x-if="isLimitSpellField(i, 'base')">
                            <div x-cloak>
                                <div :key="`limitSpell-base-${i}`" x-data="limitSpellSelect(i, spellValues[i].effect_base_value, 'base')" x-init="init()" class="flex items-center gap-2">
                                    <div x-data="ajaxSelect({ searchUrl: '/spells/search', prefillPath: '', useModal: false })" x-init="init()" class="flex-1">
                                        <select data-limit-spell x-ref="select" :name="`effect_base_display${i}`" class="w-full"></select>
                                    </div>
                                    <select class="select w-20" x-model="mode" aria-label="Include or Exclude">
                                        <option value="auto">Auto</option>
                                        <option value="exclude">Exclude</option>
                                    </select>
                                    <input type="hidden" :name="`effect_base_value${i}`" :value="signedValue" />
                                </div>
                            </div>
                        </template>
                        <div
                            x-show="isSpellField(i, 'base') && !isLimitSpellField(i, 'base')"
                            x-transition
                        >
                            <div
                                x-data="ajaxSelect({
                                    searchUrl: '/spells/search',
                                    prefillPath: '',
                                })"
                                x-init="init()"
                                :key="`spell-base-${i}`"
                            >
                                <select
                                    x-ref="select"
                                    :name="`effect_base_value${i}`"
                                    class="w-full"
                                ></select>
                            </div>
                        </div>
                        <template x-if="isRaceField(i, 'base')">
                            <div
                                x-data="raceSelect(i, spellValues[i].effect_base_value)"
                                x-init="init()"
                            >
                                <select :name="`effect_base_value${i}`" x-model.number="spellValues[i].effect_base_value"
                                    @focus="load()" @click="load()" class="select w-full">
                                    <option :value="spellValues[i].effect_base_value"
                                        x-text="display || (spellValues[i].effect_base_value ? (spellValues[i].effect_base_value + ': ' + name) : 'Select race')">
                                    </option>
                                    <template x-if="loaded">
                                        <template x-for="opt in options" :key="opt[0]">
                                            <option :value="opt[0]" x-text="opt[0] + ': ' + opt[1]"></option>
                                        </template>
                                    </template>
                                </select>
                            </div>
                        </template>
                        <template x-if="isLimitSpellTypeField(i, 'base')">
                            <select :name="`effect_base_value${i}`" x-model.number="spellValues[i].effect_base_value"
                                @focus="setSelected(i)" class="select w-full">
                                <option value="0">0: Detrimental</option>
                                <option value="1">1: Beneficial</option>
                            </select>
                        </template>
                        <template x-if="
                            !isItemField(i,'base') &&
                            !isSpellField(i,'base') &&
                            !isRaceField(i,'base') &&
                            !isLimitSpellTypeField(i,'base') &&
                            !isSpellEffectField(i,'base')
                        ">
                            <input
                                type="number"
                                :name="`effect_base_value${i}`"
                                x-model.number="spellValues[i].effect_base_value"
                                @focus="setSelected(i)" class="input w-full"
                            />
                        </template>
                        <template x-if="isSpellEffectField(i, 'base')">
                            <div x-cloak>
                                <div :key="`spellEffect-base-${i}`" x-data="spellEffectSelect(i, spellValues[i].effect_base_value, 'base')" x-init="init()" class="flex items-center gap-2">
                                    <select x-model.number="localKey" @mousedown="load()" @focus="load()" @click="load()" class="select flex-1">
                                        <option x-show="!loaded" :value="localKey" x-text="display || (localKey ? (localKey + ': ' + name) : 'Select effect')"></option>
                                        <option disabled x-show="!loaded">Loading...</option>
                                        <template x-if="loaded">
                                            <template x-for="opt in options" :key="opt[0]">
                                                <option :value="opt[0]" x-text="opt[0] + ': ' + opt[1]"></option>
                                            </template>
                                        </template>
                                    </select>
                                    <select class="select w-20" x-model="mode" aria-label="Include or Exclude">
                                        <option value="auto">Auto</option>
                                        <option value="exclude">Exclude</option>
                                    </select>
                                    <input type="hidden" :name="`effect_base_value${i}`" :value="signedValue" />
                                </div>
                            </div>
                        </template>
                    </div>
                    {{-- limit value --}}
                    <div>
                        <div
                            x-show="isItemField(i, 'limit')"
                            x-transition
                        >
                            <div
                                x-data="ajaxSelect({
                                    searchUrl: '/items/search',
                                    prefillPath: ''
                                })"
                                x-init="init()"
                                :key="`item-limit-${i}`"
                            >
                                <select
                                    x-ref="select"
                                    :name="`effect_limit_value${i}`"
                                    class="w-full"
                                ></select>
                            </div>
                        </div>
                        <div
                            x-show="isSpellField(i, 'limit')"
                            x-transition
                        >
                            <div
                                x-data="ajaxSelect({
                                    searchUrl: '/spells/search',
                                    prefillPath: '',
                                })"
                                x-init="init()"
                                :key="`spell-limit-${i}`"
                            >
                                <select
                                    x-ref="select"
                                    :name="`effect_limit_value${i}`"
                                    class="w-full"
                                ></select>
                            </div>
                        </div>
                        <template x-if="!isItemField(i,'limit') && !isSpellField(i,'limit') && !isSpellEffectField(i,'limit')">
                            <input type="number" :name="`effect_limit_value${i}`"
                                x-model.number="spellValues[i].effect_limit_value" @focus="setSelected(i)" class="input w-full" />
                        </template>
                        <template x-if="isSpellEffectField(i, 'limit')">
                            <div x-cloak>
                                <div :key="`spellEffect-limit-${i}`" x-data="spellEffectSelect(i, spellValues[i].effect_limit_value, 'limit')" x-init="init()" class="flex items-center gap-2">
                                    <select x-model.number="localKey" @mousedown="load()" @focus="load()" @click="load()" class="select flex-1">
                                        <option x-show="!loaded" :value="localKey" x-text="display || (localKey ? (localKey + ': ' + name) : 'Select effect')"></option>
                                        <option disabled x-show="!loaded">Loading...</option>
                                        <template x-if="loaded">
                                            <template x-for="opt in options" :key="opt[0]">
                                                <option :value="opt[0]" x-text="opt[0] + ': ' + opt[1]"></option>
                                            </template>
                                        </template>
                                    </select>
                                    <select class="select w-20" x-model="mode" aria-label="Include or Exclude">
                                        <option value="auto">Auto</option>
                                        <option value="exclude">Exclude</option>
                                    </select>
                                    <input type="hidden" :name="`effect_limit_value${i}`" :value="signedValue" />
                                </div>
                            </div>
                        </template>
                    </div>
                    {{-- max value --}}
                    <div>
                        <input type="number" :name="`max${i}`" x-model.number="spellValues[i].max" @focus="setSelected(i)"
                            class="input w-full" />
                    </div>
                    {{-- formula value --}}
                    <div>
                        <div class="flex items-center gap-2">
                            <input type="number" :name="`formula${i}`" x-model.number="spellValues[i].formula"
                                @focus="setSelected(i)" class="input flex-1" />
                            <button type="button" title="Open formula picker"
                                class="btn btn-soft btn-secondary"
                                @click="window.dispatchEvent(new CustomEvent('open-formula-picker',{detail:{index:i,value: spellValues[i].formula}}))">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M3 19a2 2 0 0 0 2 2c2 0 2 -4 3 -9s1 -9 3 -9a2 2 0 0 1 2 2" />
                                    <path d="M5 12h6" /><path d="M15 12l6 6" /><path d="M15 18l6 -6" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <div class="flex justify-end items-center p-2 mt-2">
        <label class="mr-2 text-sm font-medium" x-text="selectedTargetLabel()"></label>

        {{-- zones --}}
        <template x-if="selectedTargetType() === 'zones'">
            <input type="text" :name="`teleport_zone`"
                :value="(spellValues[selectedIndex] && spellValues[selectedIndex].teleport_zone) || teleport_zone || ''"
                readonly lpignore="true" class="input w-64" @click="openSelectorForSelected()" placeholder="Select zone" />
        </template>

        {{-- pets --}}
        <template x-if="selectedTargetType() === 'pets'">
            <input type="text" :name="`teleport_zone`"
                :value="(spellValues[selectedIndex] && spellValues[selectedIndex].teleport_zone) || teleport_zone || ''"
                readonly lpignore="true" class="input w-64" @click="openSelectorForSelected()" placeholder="Select pet" />
        </template>

        {{-- mounts --}}
        <template x-if="selectedTargetType() === 'horses'">
            <input type="text" :name="`teleport_zone`"
                :value="(spellValues[selectedIndex] && spellValues[selectedIndex].teleport_zone) || teleport_zone || ''"
                readonly lpignore="true" class="input w-64" @click="openSelectorForSelected()" placeholder="Select Mount" />
        </template>

        {{-- aura? --}}
        <template x-if="selectedTargetType() === 'auras'">
            <input type="text" :name="`teleport_zone`"
                :value="(spellValues[selectedIndex] && spellValues[selectedIndex].teleport_zone) || teleport_zone || ''"
                readonly lpignore="true" class="input w-64" @click="openSelectorForSelected()" placeholder="Select Aura" />
        </template>
        {{-- default --}}
        <template x-if="!selectedTargetType()">
            <input id="teleport_zone_global" name="teleport_zone" type="text" x-model="teleport_zone"
                :readonly="!isSpecial(selectedIndex)" @click="isSpecial(selectedIndex) && openSelectorForSelected()"
                lpignore="true" class="input w-64" placeholder="Teleport Zone" />
        </template>
    </div>

    <div x-show="showModal" x-transition x-cloak class="fixed inset-0 z-50 flex items-start justify-center p-6"
        style="background: rgba(0,0,0,.5);">
        <div class="bg-base-100 w-full max-w-4xl rounded shadow-lg overflow-hidden">
            <div class="flex items-center justify-between p-3 border-b border-base-content/10">
                <div class="font-semibold"
                    x-text="modalType ? modalType.charAt(0).toUpperCase() + modalType.slice(1) : ''"></div>
                <button type="button" @click="closeModal" class="btn btn-ghost btn-sm">Close</button>
            </div>

            <div class="p-4 space-y-3">
                <input type="text" class="input w-full" placeholder="Search..." x-model="modalFilter" />

                <div class="max-h-[50vh] overflow-auto">
                    <template x-if="modalType === 'zones' && $store.modalCache.zones && $store.modalCache.zones.length">
                        @include('spells.partials.teleport_zone.zones')
                    </template>
                    <template x-if="modalType === 'pets' && $store.modalCache.pets && $store.modalCache.pets.length">
                        @include('spells.partials.teleport_zone.pets')
                    </template>
                    <template
                        x-if="modalType === 'horses' && $store.modalCache.horses && $store.modalCache.horses.length">
                        @include('spells.partials.teleport_zone.horses')
                    </template>
                    <template x-if="modalType === 'auras' && $store.modalCache.auras && $store.modalCache.auras.length">
                        @include('spells.partials.teleport_zone.auras')
                    </template>

                    <template x-if="!$store.modalCache[modalType] || !$store.modalCache[modalType].length">
                        <div class="p-2 text-sm text-gray-500">Loading or no results...</div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="divider"></div>
<div class="space-y-6">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-6 gap-4 mt-4">
                <x-form.input
                    name="pushback"
                    label="Knockback Push Back"
                    tooltip="Knockback out force."
                    :value="$spell->pushback"
                    type="number"
                />
                <x-form.input
                    name="pushup"
                    label="Knockback Push Up"
                    tooltip="Knockback up force."
                    :value="$spell->pushup"
                    type="number"
                />
                <div
                    x-data='ajaxSelect({
                        searchUrl: "/spells/search",
                        useModal: false,
                        prefillValue: @json($spell->recourseLink)
                    })'
                    x-init="init()"
                >
                    <label class="label">Recourse ID</label>
                    <select
                        x-ref="select"
                        name="RecourseLink"
                        class="w-full"
                        tooltip="When this spell is cast, automatically apply the recourse spell id on caster."
                    ></select>
                </div>
                <x-form.input
                    name="bonushate"
                    label="Hate Modifier"
                    tooltip="Add or remove an additional amount of hate to this spell."
                    :value="$spell->bonushate"
                    type="number"
                />
                <x-form.input
                    name="HateAdded"
                    label="Hate Spell Hate Given"
                    tooltip="Overrides spell hate and uses this value instead."
                    :value="$spell->HateAdded"
                    type="number"
                />
                <x-form.input
                    name="maxtargets"
                    label="Focus Max Targets"
                    tooltip="Do not apply heal or damage item statistic to this spell."
                    :value="$spell->maxtargets"
                    type="number"
                    min="-1"
                />
                <x-form.input
                    name="songcap"
                    label="Focus Song Base Effect Cap"
                    tooltip="Maximum instrument/singing modifier that can be applied to a song."
                    :value="$spell->songcap"
                    type="number"
                    min="0"
                />
                <x-form.input
                    name="field217"
                    label="Max Critical Chance"
                    tooltip="Set maximum critical chance of this spell."
                    :value="$spell->field217"
                    type="number"
                    min="-1"
                />
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Nimbus Animation</h2>
            <div class="grid grid-cols-3 gap-4 mt-2">
                <div x-data="{ value: '{{ $spell->nimbuseffect ?? '' }}', videoOk: false }" class="flex flex-col items-stretch gap-2">
                    <label class="label">Nimbus Effect</label>
                    <div class="video-preview w-full min-h-65 max-h-65 flex-none rounded border border-base-content/20 bg-neutral overflow-hidden flex items-center justify-center">
                        <template x-if="value !== '' && value != 0">
                            <video x-ref="spellVid" x-bind:src="'/spell-animations/' + value + '.mp4'"
                                x-on:loadedmetadata="videoOk = true" x-on:error="videoOk = false"
                                muted loop autoplay playsinline class="w-full h-full object-contain" x-show="videoOk"></video>
                        </template>
                        <div x-show="!(value !== '' && value != 0) || !videoOk"
                            class="w-full h-full flex items-center justify-center text-neutral-400">
                            No preview
                        </div>
                    </div>
                    <div class="flex gap-2 items-center">
                        <input
                            name="nimbuseffect"
                            type="number"
                            class="input w-3/4"
                            x-model="value"
                            x-on:input="videoOk = false"
                            placeholder="Nimbus Effect ID"
                        />
                        <button
                            type="button"
                            class="btn btn-soft btn-secondary w-1/4"
                            @click="$dispatch('open-anim-picker', {
                                target: 'nimbuseffect',
                                type: 'spell'
                            })"
                        >
                            Browse
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Options</h2>
            <div class="flex flex-wrap items-center gap-4">
                <x-form.checkbox
                    name="field198"
                    label="Hate No Detrimental Spell Aggro"
                    tooltip="This spell will not cause you to be added to targets hate list."
                    :checked="$spell->field198"
                />
                <x-form.checkbox
                    name="not_extendable"
                    label="Not Focusable"
                    tooltip="This spell can not be focused."
                    :checked="$spell->not_extendable"
                />
            </div>
        </div>
    </div>
</div>
