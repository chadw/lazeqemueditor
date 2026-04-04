<div class="space-y-6">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                <x-form.range
                    name="range"
                    label="Range"
                    min="0"
                    max="1000"
                    step="1"
                    digits="4"
                    :value="$spell->range"
                    show-value
                    tooltip="Single targeted spell maximum range."
                />
                <x-form.range
                    name="min_range"
                    label="Min Range"
                    min="0"
                    max="1000"
                    step="1"
                    digits="4"
                    :value="$spell->min_range"
                    show-value
                    tooltip="Can not be closer to target than this range to cast this spell."
                />
                <x-form.input
                    name="min_dist"
                    label="(Optional) Min Distance for Mod"
                    tooltip="Closet distance you can be from target for spell to receive a modifier."
                    :value="$spell->min_dist"
                    type="number"
                    min="0"
                    step="any"
                />
                <x-form.input
                    name="min_dist_mod"
                    label="(Optional) Min Distance Mod"
                    tooltip="Modifier applied at the closet distance."
                    :value="$spell->min_dist_mod"
                    type="number"
                    min="0"
                    step="any"
                />
                <x-form.input
                    name="max_dist"
                    label="(Optional) Max Distance for Mod"
                    tooltip="Furthest distance you can be from target for spell to receive a modifier."
                    :value="$spell->max_dist"
                    type="number"
                    min="0"
                    step="any"
                />
                <x-form.input
                    name="max_dist_mod"
                    label="(Optional) Max Distance Mod"
                    tooltip="Modifier applied at the furthest distance."
                    :value="$spell->max_dist_mod"
                    type="number"
                    min="0"
                    step="any"
                />
                <x-form.select
                    name="targettype"
                    label="Target Type"
                    tooltip="Determines how the spell will be applied to targets."
                    keyInOption="true"
                    :options="config('everquest.spell_targets')"
                    :selected="$spell->targettype"
                />
                <x-form.range
                    name="ConeStartAngle"
                    label="Cone Angle Start"
                    min="0"
                    max="360"
                    step="1"
                    digits="3"
                    :value="$spell->ConeStartAngle"
                    show-value
                    tooltip="Start cone angle. 360 degree total."
                />
                <x-form.range
                    name="ConeStopAngle"
                    label="Cone Angle End"
                    min="0"
                    max="360"
                    step="1"
                    digits="3"
                    :value="$spell->ConeStopAngle"
                    show-value
                    tooltip="End of cone angle. 360 degree total."
                />
                <x-form.range
                    name="aoerange"
                    label="AOE Range"
                    min="0"
                    max="1000"
                    step="1"
                    digits="4"
                    :value="$spell->aoerange"
                    show-value
                    tooltip="Area of effect maximum range."
                />
                <x-form.input
                    name="AEDuration"
                    label="AOE Rain Waves"
                    tooltip="Number of rain wave is, 1 wave = 2500."
                    :value="$spell->AEDuration"
                    type="number"
                    min="0"
                />
                <x-form.input
                    name="aemaxtargets"
                    label="AOE Max Targets"
                    tooltip="Maximum targets that any type of area of effect spell can hit."
                    :value="$spell->aemaxtargets"
                    type="number"
                    min="-1"
                />
                <x-form.select
                    name="numhitstype"
                    label="Max Hits Type"
                    tooltip="Defines which type of behavior will increment down down the buff limited use counter."
                    keyInOption="true"
                    :options="[0 => 'None'] + config('everquest.spell_numhits_type')"
                    :selected="$spell->numhitstype"
                    wrapper-class="col-span-2"
                />
                <x-form.input
                    name="numhits"
                    label="Max Hits Allowed"
                    tooltip="The amount of limit use counts the buff will have."
                    :value="$spell->numhits"
                    type="number"
                    min="0"
                />
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Options</h2>
            <div class="flex flex-wrap items-center gap-4">
                <x-form.checkbox
                    name="npc_no_los"
                    label="NPC Line of Sight Not Required to Cast"
                    tooltip="Does this spell require line of sight to target."
                    :checked="$spell->npc_no_los"
                />
            </div>
        </div>
    </div>
</div>
