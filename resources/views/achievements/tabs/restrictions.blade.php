<div class="space-y-4">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h2 class="card-title">Cast Requirements</h2>
                    <p class="text-sm opacity-65">
                        Add achievement_cast_requirements rows for existing spell restriction IDs. Reusing one restriction ID across achievements
                        creates an AND condition: every attached completion expectation must match.
                    </p>
                </div>
                <button type="button" class="btn btn-sm btn-soft btn-success" @click="addRestriction()"
                    title="Attach another spell cast-restriction identity">
                    <x-ui.icon name="add" /> Add Restriction
                </button>
            </div>

            <div class="alert alert-soft alert-warning py-2 mt-3 text-sm">
                Safe Clone intentionally omits these rows so a copied draft cannot silently alter live spell eligibility.
            </div>

            <div class="space-y-3 mt-3">
                <template x-for="(restriction, index) in editor.restrictions" :key="restriction._uid">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end rounded-box border border-base-content/10 bg-base-200 p-3">
                        <div class="form-control md:col-span-5">
                            <label class="label">
                                <span>Restriction ID <x-ui.field-help text="Spell cast-restriction identity consumed by EQEmu. One ID may reference several achievements, which are evaluated together." /></span>
                            </label>
                            <input type="number" min="1" max="4294967295" class="input w-full tabular-nums"
                                :name="`restrictions[${index}][restriction_id]`" x-model.number="restriction.restriction_id" required>
                        </div>
                        <div class="md:col-span-6 min-h-12 flex items-center">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" class="toggle toggle-success" x-model="restriction.requires_completed">
                                <input type="hidden" :name="`restrictions[${index}][requires_completed]`"
                                    :value="restriction.requires_completed ? 1 : 0">
                                <span x-text="restriction.requires_completed ? 'Character must have completed this achievement' : 'Character must not have completed this achievement'"></span>
                                <x-ui.field-help text="Choose whether this achievement's durable completion state must be present or absent for the cast restriction to pass." />
                            </label>
                        </div>
                        <button type="button" class="btn btn-soft btn-error md:col-span-1"
                            @click="if (confirm('Remove this mapping? Spells using this restriction ID may become eligible after the achievement catalog reloads.')) remove(editor.restrictions, index)"
                            aria-label="Remove cast restriction"><x-ui.icon name="delete" /></button>
                    </div>
                </template>
            </div>

            <div x-show="editor.restrictions.length === 0" class="rounded-box border border-dashed border-base-content/20 p-4 mt-3 text-center text-sm opacity-65">
                No spell cast requirements reference this achievement.
            </div>
        </div>
    </div>
</div>
