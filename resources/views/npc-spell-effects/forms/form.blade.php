<div class="card bg-base-200 card-sm shadow-sm" x-data="formTracker">
    <div class="card-body">
        <h2 class="card-title">NPC Spell Effect</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <x-form.input
                name="name"
                label="Name"
                tooltip=""
                :value="$npcSpellEffect->name"
            />
            <x-form.input
                name="parent_list"
                label="Parent"
                type="number"
                tooltip=""
                :value="$npcSpellEffect->parent_list"
            />
            <div class="mt-4 flex gap-2">
                <button type="submit" class="btn btn-soft btn-success">
                    Save NPC Spell Effect
                </button>
            </div>
        </div>
    </div>
</div>
