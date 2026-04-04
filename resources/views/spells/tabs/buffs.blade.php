<div class="space-y-6">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-8 gap-4">
                <x-form.input
                    name="buffduration"
                    label="Buff Duration"
                    tooltip="Duration of the buff in tics, 1 tic = 6 seconds."
                    :value="$spell->buffduration"
                    type="number"
                    min="0"
                />
                <x-form.select
                    name="buffdurationformula"
                    label="Buff Duration Formula"
                    tooltip="Determines actual buff duration."
                    :options="config('everquest.spell_buff_duration_formulas')"
                    :selected="$spell->buffdurationformula"
                    wrapper-class="col-span-2"
                    keyInOption=true
                />
                <x-form.input
                    name="viral_range"
                    label="Viral Range"
                    tooltip="Maximum range for a viral spell to spread to another target."
                    :value="$spell->viral_range"
                    type="number"
                    min="0"
                />
                <x-form.input
                    name="viral_targets"
                    label="Viral Targets"
                    tooltip="Maximum viral spread time. Actual time is a random between max and min."
                    :value="$spell->viral_targets"
                    type="number"
                    min="0"
                />
                <x-form.input
                    name="viral_timer"
                    label="Viral Timer"
                    tooltip="Minimum viral spread time. Actual time is a random between max and min."
                    :value="$spell->viral_timer"
                    type="number"
                    min="0"
                />
                <x-form.input
                    name="pvp_duration"
                    label="PVP Duration"
                    tooltip=""
                    :value="$spell->pvp_duration"
                    type="number"
                    min="0"
                />
                <x-form.input
                    name="pvp_duration_cap"
                    label="PVP Duration Cap"
                    tooltip=""
                    :value="$spell->pvp_duration_cap"
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
                    name="nodispell"
                    label="Can Not Dispell"
                    tooltip="This buff can not be dispelled."
                    :checked="$spell->nodispell"
                />
                <x-form.checkbox
                    name="field232"
                    label="Can Not Click Off"
                    tooltip="Can not click off this buff."
                    :checked="$spell->field232"
                />
                <x-form.checkbox
                    name="persistdeath"
                    label="Persist After Death"
                    tooltip="Buff will not fade if you die."
                    :checked="$spell->persistdeath"
                />
                <x-form.checkbox
                    name="suspendable"
                    label="Suspendable"
                    tooltip=""
                    :checked="$spell->suspendable"
                />
                <x-form.checkbox
                    name="can_mgb"
                    label="Can MGB"
                    tooltip="This buff can be mass group buffed."
                    :checked="$spell->can_mgb"
                />
                <x-form.checkbox
                    name="short_buff_box"
                    label="Appear In Short Buff Box"
                    tooltip="Use short duration buff box."
                    :checked="$spell->short_buff_box"
                />
                <x-form.checkbox
                    name="no_block"
                    label="No Buff Block"
                    tooltip="Can not set this as a blocked buff."
                    :checked="$spell->no_block"
                />
                <x-form.checkbox
                    name="dot_stacking_exempt"
                    label="DOT Not Stackable"
                    tooltip=""
                    :checked="$spell->dot_stacking_exempt"
                />
            </div>
        </div>
    </div>
</div>
