<div class="card bg-base-200 card-sm shadow-sm">
    <div class="card-body">
        <div class="grid grid-cols-1 gap-4 mb-2">
            <input
                type="hidden"
                name="npc_spells_effects_id"
                :value="$store.modalForm.form.npc_spells_effects_id"
            />
            <x-form.select
                name="spell_effect_id"
                label="Spell Effect"
                tooltip=""
                :options="config('everquest.spell_effects')"
                keyInOption="true"
                x-model="$store.modalForm.form.spell_effect_id"
            />
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4">
            <x-form.input
                name="minlevel"
                label="Min Level"
                x-model="$store.modalForm.form.minlevel"
            />
            <x-form.input
                name="maxlevel"
                label="Max Level"
                x-model="$store.modalForm.form.maxlevel"
            />
            <x-form.input
                name="se_base"
                label="SE Base"
                x-model="$store.modalForm.form.se_base"
            />
            <x-form.input
                name="se_limit"
                label="SE Limit"
                x-model="$store.modalForm.form.se_limit"
            />
            <x-form.input
                name="se_max"
                label="SE Max"
                x-model="$store.modalForm.form.se_max"
            />
        </div>
    </div>
</div>
