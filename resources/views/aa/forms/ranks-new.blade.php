@php
    $levels = collect(range(1, 110))->mapWithKeys(fn ($v) => [$v => $v])->toArray();
@endphp
<div x-data="formTracker">
    <div class="card bg-base-200 card-sm shadow-sm mb-4">
        <div class="card-body">
            <div class="grid grid-cols-7 gap-4">
                <x-form.input
                    name="cost"
                    label="Cost"
                    type="number"
                    min="0"
                    :value="$rank['cost']"
                />
                <x-form.select
                    name="level_req"
                    label="Level Req"
                    :options="$levels"
                    :selected="$rank['level_req']"
                />
                <x-form.select
                    name="expansion"
                    label="Expansion"
                    :options="[-1 => 'All'] + config('everquest.expansions')"
                    :selected="$rank['expansion']"
                />
                <div
                    x-data="ajaxSelect({
                        searchUrl: '/spells/search',
                        prefillValue: @js([
                            'id' => $rank['spell_']['id'] ?? '-1',
                            'name' => $rank['spell_']['name'] ?? 'Unknown',
                            'new_icon' => $rank['spell_']['new_icon'] ?? null
                        ]),
                        allowNone: true,
                        noneLabel: 'No Spell',
                        noneId: -1,
                    })"
                    x-init="init()"
                    class="col-span-2"
                >
                    <label class="label">Spell</label>
                    <select
                        x-ref="select"
                        name="spell"
                        class="w-full"
                    ></select>
                </div>
                <x-form.input
                    name="spell_type"
                    label="Spell Type"
                    type="number"
                    min="0"
                    :value="$rank['spell_type']"
                />
                {{--
                <x-form.select
                    name="spell_type"
                    label="Spell Type"
                    :options="[0 => 'None'] + config('everquest.spell_types')"
                    :selected="$rank['spell_type']"
                />
                --}}
                <x-form.progress-input
                    name="recast_time"
                    label="Recast Time (s)"
                    class="w-20!"
                    tooltip=""
                    unit="s"
                    :value="$rank['recast_time']"
                />
                <x-form.input
                    name="prev_id"
                    label="Prev ID"
                    type="number"
                    min="-1"
                    readonly
                    tooltip="Readonly. Automatically set to the ID of the previous rank."
                    :value="(collect($allRanks ?? [])->last()->id ?? -1)"
                />
                <x-form.input
                    name="next_id"
                    label="Next ID"
                    type="number"
                    min="-1"
                    :value="$rank['next_id']"
                />
                <div x-data="dbstrLookup(1, 'title_sid')">
                    <label class="label">Title SID</label>
                    <div class="join">
                        <x-form.input
                            name="title_sid"
                            type="number"
                            min="-1"
                            :value="$rank['title_sid']"
                            wrapper-class="join-item"
                        />
                        <button type="button" class="btn btn-soft btn-secondary join-item"
                            @click="$store.dbstrPicker.open('title_sid', 1)">
                            <x-ui.icon name="search" />
                        </button>
                    </div>
                </div>
                <div x-data="dbstrLookup(4, 'desc_sid')">
                    <label class="label">Desc SID</label>
                    <div class="join">
                        <x-form.input
                            name="desc_sid"
                            type="number"
                            min="-1"
                            :value="$rank['desc_sid']"
                            wrapper-class="join-item"
                        />
                        <button type="button" class="btn btn-soft btn-secondary join-item"
                            @click="$store.dbstrPicker.open('desc_sid', 4)">
                            <x-ui.icon name="search" />
                        </button>
                    </div>
                </div>
                <div x-data="dbstrLookup(2, 'lower_hotkey_sid')">
                    <label class="label">Lower Hotkey SID</label>
                    <div class="join">
                        <x-form.input
                            name="lower_hotkey_sid"
                            type="number"
                            min="-1"
                            :value="$rank['lower_hotkey_sid']"
                        />
                        <button type="button" class="btn btn-soft btn-secondary join-item"
                            @click="$store.dbstrPicker.open('lower_hotkey_sid', 2)">
                            <x-ui.icon name="search" />
                        </button>
                    </div>
                </div>
                <div x-data="dbstrLookup(3, 'upper_hotkey_sid')">
                    <label class="label">Upper Hotkey SID</label>
                    <div class="join">
                        <x-form.input
                            name="upper_hotkey_sid"
                            type="number"
                            min="-1"
                            :value="$rank['upper_hotkey_sid']"
                        />
                        <button type="button" class="btn btn-soft btn-secondary join-item"
                            @click="$store.dbstrPicker.open('upper_hotkey_sid', 3)">
                            <x-ui.icon name="search" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
