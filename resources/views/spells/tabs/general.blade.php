<div class="space-y-6">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-5 gap-4">
                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text">ID <span class="text-error">*</span></span>
                    </label>
                    <div x-data class="flex gap-2">
                        <input
                            id="spell_id_field" name="id" type="number"
                            value="{{ old('id', $spell->id) }}"
                            required
                            class="input w-full"
                        />
                        <button
                            type="button"
                            class="btn btn-soft btn-secondary"
                            @click='$store.idPicker.open({
                                selector: "#spell_id_field",
                                type: "spells"
                            })'>
                            Pick
                        </button>
                    </div>
                </div>
                <x-form.input
                    name="name"
                    label="Name"
                    :value="$spell->name"
                    required
                    wrapper-class="col-span-3"
                />
                <div
                    x-data="{ value: '{{ $spell->new_icon }}' }"
                    class="join flex items-center gap-2 mt-4"
                >
                    <div
                        class="join-item w-10 h-10 min-w-10 border rounded border-base-content/20 bg-base-200"
                        :class="value ? 'spell-icon spell-' + value + ' rounded-lg {{ config('everquest.spell_target_colors.' . $spell->targettype, '') }}': ''"
                    ></div>
                    <input
                        id="itemIcon"
                        name="new_icon"
                        type="number"
                        min="0"
                        class="join-item input w-full"
                        x-model="value"
                        data-icon-range="spells"
                        data-preview="blur"
                    />
                    <button
                        type="button"
                        class="join-item btn btn-soft btn-secondary"
                        @click="$store.iconPicker.open('itemIcon')"
                    >
                        Pick
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div>
        <div class="flex flex-wrap gap-1">
            @foreach (config('everquest.classes') as $column => $label)
                @php
                    $value = old('classes' . $column, $spell->{'classes' . $column});
                    $short = config('everquest.classes_abbr.' . $column);
                @endphp

                <div class="flex flex-col items-center gap-0.5 p-1 rounded border border-neutral-700 bg-base-200
                    {{ $value == 0 ? 'opacity-50' : '' }}"
                >
                    <span class="text-[10px] font-semibold leading-none text-neutral-300">
                        {{ $short }}
                    </span>
                    <span class="" title="{{ $label }}">
                        <span class="item-icon item-{{ config('everquest.classes_icons.' . $column) }}"></span>
                    </span>
                    <input
                        type="number"
                        name="classes{{ $column }}"
                        value="{{ $value }}"
                        min="0"
                        max="255"
                        inputmode="numeric"
                        class="input w-14 text-center"
                        data-preview="blur"
                    />
                </div>
            @endforeach
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-4 gap-4 mb-2">
                <x-form.input
                    name="you_cast"
                    label="You Cast"
                    tooltip="Message when you cast the spell."
                    :value="$spell->you_cast"
                    data-preview="blur"
                />
                <x-form.input
                    name="other_casts"
                    label="Other Casts"
                    tooltip="Message other receive when you cast the spell."
                    :value="$spell->other_casts"
                    data-preview="blur"
                />
                <x-form.input
                    name="cast_on_you"
                    label="Cast on You"
                    tooltip="Message when you cast the spell on yoursef."
                    :value="$spell->cast_on_you"
                    data-preview="blur"
                />
                <x-form.input
                    name="cast_on_other"
                    label="Cast on Other"
                    tooltip="Message when spell is cast on others."
                    :value="$spell->cast_on_other"
                    data-preview="blur"
                />
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                <x-form.input
                    name="spell_fades"
                    label="Spell Fades"
                    tooltip="Message when your spell buff fades."
                    :value="$spell->spell_fades"
                    data-preview="blur"
                />
                <x-form.input
                    name="player_1"
                    label="ID File"
                    tooltip="Text used to describe player vs npc spells. Can put any text here."
                    :value="$spell->player_1"
                />
                <x-form.select
                    name="skill"
                    label="Skills"
                    tooltip="Spells skill type."
                    :options="config('everquest.db_skills')"
                    :selected="$spell->skill"
                    data-preview="blur"
                />
                <x-form.select
                    name="goodEffect"
                    label="Good Effect"
                    tooltip="Determines if spell is beneficial or detrimental to the target."
                    :options="[
                        0 => 'Detrimental',
                        1 => 'Beneficial',
                        2 => 'Beneficial Group Only',
                        3 => 'Beneficial Group Only',
                    ]"
                    :selected="$spell->goodEffect"
                    data-preview="blur"
                />
                <x-form.input
                    name="mana"
                    label="Mana Cost"
                    tooltip="How much mana is required to cast this spell."
                    :value="$spell->mana"
                    type="number"
                    min="0"
                    data-preview="live"
                />
                <x-form.input
                    name="EndurCost"
                    label="Endurance Cost"
                    tooltip="Instant endurance cost."
                    :value="$spell->EndurCost"
                    type="number"
                    min="0"
                    data-preview="live"
                />
                <x-form.input
                    name="EndurUpkeep"
                    label="Endurance Upkeep"
                    tooltip="Endurance drain per second when a discipline is active."
                    :value="$spell->EndurUpkeep"
                    type="number"
                    min="0"
                    data-preview="live"
                />
                <x-form.input
                    name="EndurTimerIndex"
                    label="Endurance Timer"
                    tooltip="Discipline timer id and timer id used for linked spells. Max is 19."
                    :value="$spell->EndurTimerIndex"
                    type="number"
                    min="-1"
                    data-preview="live"
                />
                <x-form.input
                    name="spellgroup"
                    label="Group ID"
                    tooltip="Assign group id to ranked spells."
                    :value="$spell->spellgroup"
                    type="number"
                    data-preview="live"
                />
                <x-form.input
                    name="rank"
                    label="Rank"
                    tooltip="Assign rank id, typically 1=Rank I, 5=Rank II, 10=Rank III. AA spell clicks also are assigned rank."
                    :value="$spell->rank"
                    type="number"
                />
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Animation Previews</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-2">
                <div x-data="{ value: '{{ $spell->spellanim ?? '' }}', videoOk: false }" class="flex flex-col items-stretch gap-2">
                    <label class="label">Spell Animation</label>
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
                            name="spellanim"
                            type="number"
                            class="input w-3/4"
                            x-model="value"
                            x-on:input="videoOk = false"
                            placeholder="Spell Animation ID"
                        />
                        <button
                            type="button"
                            class="btn btn-soft btn-secondary w-1/4"
                            @click="$dispatch('open-anim-picker', {
                                target: 'spellanim',
                                type: 'spell'
                            })"
                        >
                            Browse
                        </button>
                    </div>
                </div>
                <div x-data="{ value: '{{ $spell->CastingAnim ?? '' }}', videoOk: false }" class="flex flex-col items-stretch gap-2">
                    <label class="label">Casting Animation</label>
                    <div class="video-preview w-full min-h-65 max-h-65 flex-none rounded border border-base-content/20 bg-neutral overflow-hidden flex items-center justify-center">
                        <template x-if="value !== '' && value != 0">
                            <video x-ref="castVid" x-bind:src="'/player-animations/' + value + '.mp4'"
                                x-on:loadedmetadata="videoOk = true" x-on:error="videoOk = false"
                                muted loop autoplay playsinline class="w-full h-full object-contain" x-show="videoOk"></video>
                        </template>
                        <div x-show="!(value !== '' && value != 0) || !videoOk" class="w-full h-full flex items-center justify-center text-neutral-400">No preview</div>
                    </div>
                    <div class="flex gap-2 items-center">
                        <input
                            name="CastingAnim"
                            type="number"
                            class="input w-3/4"
                            x-model="value"
                            x-on:input="videoOk = false"
                            placeholder="Casting Animation ID"
                        />
                        <button
                            type="button"
                            class="btn btn-soft btn-secondary w-1/4"
                            @click="$dispatch('open-anim-picker', {
                                target: 'CastingAnim',
                                type: 'player'
                            })"
                        >
                            Browse
                        </button>
                    </div>
                </div>
                <div x-data="{ value: '{{ $spell->TargetAnim ?? '' }}', videoOk: false }" class="flex flex-col items-stretch gap-2">
                    <label class="label">Target Animation</label>
                    <div class="video-preview w-full min-h-65 max-h-65 flex-none rounded border border-base-content/20 bg-neutral overflow-hidden flex items-center justify-center">
                        <template x-if="value !== '' && value != 0">
                            <video x-ref="targetVid" x-bind:src="'/player-animations/' + value + '.mp4'"
                                x-on:loadedmetadata="videoOk = true" x-on:error="videoOk = false"
                                muted loop autoplay playsinline class="w-full h-full object-contain" x-show="videoOk"></video>
                        </template>
                        <div x-show="!(value !== '' && value != 0) || !videoOk" class="w-full h-full flex items-center justify-center text-neutral-400">No preview</div>
                    </div>
                    <div class="flex gap-2 items-center">
                        <input
                            name="TargetAnim"
                            type="number"
                            class="input w-3/4"
                            x-model="value"
                            x-on:input="videoOk = false"
                            placeholder="Target Animation ID"
                        />
                        <button
                            type="button"
                            class="btn btn-soft btn-secondary w-1/4"
                            @click="$dispatch('open-anim-picker', {
                                target: 'TargetAnim',
                                type: 'player'
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
            <h2 class="card-title">DBStr Fields</h2>
            <div class="grid grid-cols-4 gap-4">
                <x-form.select
                    name="typedescnum"
                    label="Primary Category"
                    tooltip="Primary spellbook shortcut description: From dbstr_us.text type 5"
                    :options="['0' => 'None'] + $dbstr"
                    :selected="$spell->typedescnum"
                />
                <x-form.select
                    name="effectdescnum"
                    label="Second Category #1"
                    tooltip="Secondary spellbook shortcut description: From dbstr_us.text type 5"
                    :options="['0' => 'None'] + $dbstr"
                    :selected="$spell->effectdescnum"
                />
                <x-form.select
                    name="effectdescnum2"
                    label="Second Category #2"
                    tooltip="Secondary spellbook shortcut description: From dbstr_us.text type 5"
                    :options="['0' => 'None'] + $dbstr"
                    :selected="$spell->effectdescnum2"
                />
                <div class="form-control w-full" x-data="dbstrLookup(6, 'descnum')">
                    <label class="label">
                        <span class="label-text">Spell Description ID</span>
                    </label>
                    <div class="tooltip tooltip-neutral w-full block" data-tip="Spell description: From dbstr_us.text type 6">
                        <div class="flex gap-2">
                            <input
                                id="descnum"
                                name="descnum"
                                type="number"
                                min="0"
                                value="{{ old('descnum', $spell->descnum) }}"
                                class="input w-full text-right tabular-nums"
                                data-preview="blur"
                            />
                            <button type="button"
                                class="btn btn-soft btn-secondary"
                                @click="$store.dbstrPicker.open('descnum', 6)">
                                Browse
                            </button>
                        </div>
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
                    name="IsDiscipline"
                    label="Is Discipline"
                    tooltip="Determine if this spell goes into discipline window."
                    :checked="$spell->IsDiscipline"
                />
            </div>
        </div>
    </div>
</div>
