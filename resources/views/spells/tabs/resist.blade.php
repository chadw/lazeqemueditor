<div class="space-y-6">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-7 gap-4">
                <x-form.select
                    name="resisttype"
                    label="Resist Type"
                    tooltip="Type of resistance stat on target the spell is checked against to determine if lands successfully."
                    :options="config('everquest.db_elements')"
                    :selected="$spell->resisttype"
                />
                <x-form.input
                    name="ResistDiff"
                    label="Resist Diff"
                    tooltip="Decrease resist chance if negative, increases if positive."
                    :value="$spell->ResistDiff"
                    type="number"
                />
                <x-form.input
                    name="pvpresistbase"
                    label="PVP Resist Mod"
                    tooltip="not implemented"
                    :value="$spell->pvpresistbase"
                    type="number"
                />
                <x-form.input
                    name="pvpresistcalc"
                    label="PVP Resist Per Level"
                    tooltip="not implemented"
                    :value="$spell->pvpresistcalc"
                    type="number"
                />
                <x-form.input
                    name="pvpresistcap"
                    label="PVP Resist Cap"
                    tooltip="not implemented"
                    :value="$spell->pvpresistcap"
                    type="number"
                />
                <x-form.input
                    name="MinResist"
                    label="Resist Chance Limits: Min Chance"
                    tooltip="Minimum chance for a spell to be resisted."
                    :value="$spell->MinResist"
                    type="number"
                />
                <x-form.input
                    name="MaxResist"
                    label="Resist Chance Limits: Max Chance"
                    tooltip="Maximum chance for a spell to be resisted."
                    :value="$spell->MaxResist"
                    type="number"
                />
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Options</h2>
            <div class="flex flex-wrap items-center gap-4">
                <x-form.checkbox
                    name="no_partial_resist"
                    label="No Partial Resists"
                    tooltip="Spells can not do partial damage."
                    :checked="((int) ($spell->no_partial_resist ?? 0)) === 1"
                />
                <x-form.checkbox
                    name="reflectable"
                    label="Reflectable"
                    tooltip="Allow this spell to be reflected back at caster."
                    :checked="((int) ($spell->reflectable ?? 0)) === 1"
                />
                <x-form.checkbox
                    name="field160"
                    label="Feedbackable"
                    tooltip="Allow this spell to be affected by spell damage shields."
                    :checked="((int) ($spell->field160 ?? 0)) === 1"
                />
            </div>
        </div>
    </div>
</div>
