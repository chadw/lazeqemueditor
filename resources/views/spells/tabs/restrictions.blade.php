<div class="space-y-6">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                <x-form.select
                    name="CastRestriction"
                    label="Target Restriction"
                    tooltip="Special conditions on the caster that must be met in order to cast this spell."
                    :options="config('everquest.spell_target_restrictions')"
                    :selected="$spell->CastRestriction"
                />
                <x-form.select
                    name="field220"
                    label="Caster Restriction"
                    tooltip="Special conditions on the caster that must be met in order to cast this spell."
                    :options="config('everquest.spell_target_restrictions')"
                    :selected="$spell->field220"
                />
                <x-form.select
                    name="zonetype"
                    label="Zone Type"
                    tooltip="Restrict spells to be casting only within indoor zones or outdoor zones."
                    :options="config('everquest.spell_zone_type')"
                    :selected="$spell->zonetype"
                />
                <x-form.select
                    name="TimeOfDay"
                    label="Time of Day"
                    tooltip="Restrict spells to only being cast during certain times of day."
                    :options="[
                        0 => 'Any',
                        1 => 'Day',
                        2 => 'Night',
                    ]"
                    :selected="$spell->TimeOfDay"
                />
                <x-form.select
                    name="pcnpc_only_flag"
                    label="PC or NPC Only"
                    tooltip="Restrict what tpe of entity the spell can be applied to. Set who spell can apply to"
                    :options="config('everquest.spell_pcnpc_only_flag')"
                    :selected="$spell->pcnpc_only_flag"
                />
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Options</h2>
            <div class="flex flex-wrap items-center gap-4">
                <x-form.checkbox
                    name="OutofCombat"
                    label="Can Cast out of Combat"
                    tooltip="Spell can only be cast if out of combat."
                    :checked="$spell->OutofCombat"
                />
                <x-form.checkbox
                    name="InCombat"
                    label="Can Cast in Combat"
                    tooltip="Spell can only be cast if in combat."
                    :checked="$spell->InCombat"
                />
                <x-form.checkbox
                    name="allowrest"
                    label="Detrimental Spell Allows Rest"
                    tooltip="Prevent detrimental spell from canceling rest state."
                    :checked="$spell->allowrest"
                />
                <x-form.checkbox
                    name="disallow_sit"
                    label="Cancel on Sit"
                    tooltip="Buff will fade if you sit."
                    :checked="$spell->disallow_sit"
                />
                <x-form.checkbox
                    name="sneaking"
                    label="Must be Sneaking"
                    tooltip="This spell can only be applied from a sneak attack."
                    :checked="$spell->sneaking"
                />
            </div>
        </div>
    </div>
</div>
