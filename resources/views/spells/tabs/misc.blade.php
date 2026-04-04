<div class="space-y-6">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                <x-form.input
                    name="LightType"
                    label="Light Type"
                    tooltip=""
                    :value="$spell->LightType"
                    type="number"
                />
                <x-form.input
                    name="TravelType"
                    label="Travel Type"
                    tooltip="Unused?"
                    :value="$spell->TravelType"
                    type="number"
                />
                <x-form.input
                    name="ldon_trap"
                    label="LDON Trap"
                    tooltip="Flag found on all LDON trap or chest related spells."
                    :value="$spell->ldon_trap"
                    type="number"
                />
                <x-form.select
                    name="spell_category"
                    label="Spell Category"
                    tooltip=""
                    :options="config('everquest.spell_categories')"
                    :selected="$spell->spell_category"
                    keyInOption=true
                />
                <x-form.select
                    name="npc_category"
                    label="NPC Category"
                    tooltip=""
                    :options="config('everquest.spell_npc_categories')"
                    :selected="$spell->npc_category"
                    keyInOption=true
                />
                <x-form.input
                    name="npc_usefulness"
                    label="NPC Usefulness"
                    tooltip=""
                    :value="$spell->npc_usefulness"
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
                    name="deleteable"
                    label="Deleteable"
                    tooltip=""
                    :checked="$spell->deleteable"
                />
                <x-form.checkbox
                    name="Activated"
                    label="Activated"
                    tooltip=""
                    :checked="$spell->Activated"
                />
            </div>
        </div>
    </div>
</div>
