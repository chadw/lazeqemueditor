@php
    $effects = [
        [
            'type' => 'Scroll',
            'id' => 'scrolleffect',
            'lvl' => 'scrolllevel',
            'req' => 'scrolllevel2',
            'rel' => 'scrollEffectSpell'
        ],
        [
            'type' => 'Click',
            'id' => 'clickeffect',
            'lvl' => 'clicklevel',
            'req' => 'clicklevel2',
            'rel' => 'clickEffectSpell'
        ],
        [
            'type' => 'Proc',
            'id' => 'proceffect',
            'lvl' => 'proclevel',
            'req' => 'proclevel2',
            'rel' => 'procEffectSpell'
        ],
        [
            'type' => 'Focus',
            'id' => 'focuseffect',
            'lvl' => 'focuslevel',
            'req' => 'focuslevel2',
            'rel' => 'focusEffectSpell'
        ],
        [
            'type' => 'Worn',
            'id' => 'worneffect',
            'lvl' => 'wornlevel',
            'req' => 'wornlevel2',
            'rel' => 'wornEffectSpell'
        ],
        [
            'type' => 'Bard',
            'id' => 'bardeffect',
            'lvl' => 'bardlevel',
            'req' => 'bardlevel2',
            'rel' => 'bardEffectSpell'
        ],
    ];
@endphp
<div class="space-y-6">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-[70px_1fr_90px_90px] gap-3 p-2 rounded font-semibold border-b border-base-content/10">
                <div>Effect</div>
                <div>Spell ID</div>
                <div>As Level</div>
                <div>Req Level</div>
            </div>
            @foreach($effects as $effect)
                <div class="grid grid-cols-[70px_1fr_90px_90px] gap-3 items-center p-2 border-b border-base-content/5 last:border-0">
                    <div class="font-medium text-base">{{ $effect['type'] }}</div>
                        <div x-data="ajaxSelect({
                                searchUrl: '/spells/search',
                                useModal: false,
                                prefillValue: @js([
                                    'id' => $item->{$effect['id']},
                                    'name' => $item->{$effect['rel']}?->name ?? ($item->{$effect['id']} > 0
                                        ? 'Unknown Spell (' . $item->{$effect['id']} . ')' : null),
                                    'new_icon' => $item->{$effect['rel']}?->new_icon ?? null,
                                ]),
                                keyInOption: true,
                                allowNone: true,
                                noneId: -1,
                            })"
                            x-init="init()"
                        >
                        <select
                            x-ref="select"
                            name="{{ $effect['id'] }}"
                            class="w-full validator invalid:select-error"
                        ></select>
                    </div>
                    <x-form.input
                        name="{{ $effect['lvl'] }}"
                        type="number"
                        min="0"
                        :value="$item->{$effect['lvl']}"
                    />
                    <x-form.input
                        name="{{ $effect['req'] }}"
                        type="number"
                        min="0"
                        :value="$item->{$effect['req']}"
                    />
                </div>
            @endforeach
        </div>
    </div>
</div>
<div class="space-y-6">
    <div x-data='fieldWatcher("worneffect", @json(["cmp" => -1, "extraRule" => $wornAdditiveType]))' x-show="isNot()">
        <div class="card bg-base-200 card-sm shadow-sm mt-4">
            <div class="card-body">
                <h2 class="card-title">Worn Effect</h2>
                <div class="grid grid-cols-1 gap-2">
                    <div class="flex items-center gap-6 mt-2">
                        <label class="inline-flex items-center">
                            <input type="radio" name="worntype" value="0" class="radio radio-sm mr-2" @checked($item->worntype == 0) />
                            <span>None</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="worntype" value="1" class="radio radio-sm mr-2" @checked($item->worntype == 1) />
                            <span>Unknown</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="worntype" value="2" class="radio radio-sm mr-2" @checked($item->worntype == 2) />
                            <span>Worn</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="worntype" value="{{ $wornAdditiveType }}" class="radio radio-sm mr-2" @checked($item->worntype == $wornAdditiveType) />
                            <span>Stacking</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div x-data="fieldWatcher('clickeffect', -1)" x-show="isNot()">
        <div class="card bg-base-200 card-sm shadow-sm mt-4">
            <div class="card-body">
                <h2 class="card-title">Click Effect</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                    <x-form.select
                        name="clicktype"
                        label="Click Type"
                        tooltip="The maximum charges for the Click Effect on this item. Setting a value of '-1' will
                            make charges unlimited."
                        :options="[
                            0 => 'None',
                            1 => 'Clickable from Inventory with Level',
                            3 => 'Expendable',
                            4 => 'Must Equip to Click',
                            5 => 'Clickable from Inventory with Level, Race, and Class',
                        ]"
                        :selected="$item->clicktype"
                    />
                    <x-form.select
                        name="maxcharges"
                        label="Charges"
                        tooltip="The maximum charges for the Click Effect on this item. Setting a value of '-1' will
                            make charges unlimited."
                        :options="config('everquest.click_charges')"
                        :selected="$item->maxcharges"
                    />
                    <x-form.progress-input
                        name="casttime"
                        label="Cast Time (ms)"
                        tooltip=""
                        :value="$item->casttime"
                    />
                    <x-form.progress-input
                        name="recastdelay"
                        label="Recast Time (seconds)"
                        tooltip=""
                        unit="s"
                        :value="$item->recastdelay"
                    />
                    <x-form.input
                        name="recasttype"
                        label="Recast Type"
                        tooltip="(-1 = None) This is the group that the recast delay will be used in. All clickable
                            items in this same group will also be required to wait until the recast delay is done."
                        type="number"
                        min="-1"
                        :value="$item->recasttype"
                    />
                </div>
            </div>
        </div>
    </div>
    <div x-data="fieldWatcher('proceffect', -1)" x-show="isNot()">
        <div class="card bg-base-200 card-sm shadow-sm mt-4">
            <div class="card-body">
                <h2 class="card-title">Proc Effect</h2>
                <x-form.select
                    name="procrate"
                    label="Proc Rate"
                    tooltip="The percentage that a weapon will proc. (0 = Normal, 50 = 150%)"
                    :options="config('everquest.proc_rate')"
                    keyInOption="true"
                    :selected="$item->procrate"
                />
            </div>
        </div>
    </div>
</div>
