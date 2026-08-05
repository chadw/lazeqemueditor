<div class="card bg-base-100 shadow">
    <div class="card-body">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="card-title inline-flex items-center gap-1">
                    Category Associations
                    <x-ui.field-help text="An achievement may appear in multiple categories, but each category and achievement pair must be unique." />
                </h2>
                <p class="text-sm opacity-65">One definition may appear in multiple client categories.</p>
            </div>
            <button type="button" class="btn btn-sm btn-soft btn-success" @click="addAssociation()"
                title="Add another category association" aria-label="Add another category association">
                <x-ui.icon name="add" /> Add Association
            </button>
        </div>

        <div class="space-y-3 mt-3">
            <template x-for="(association, index) in editor.associations" :key="association._uid">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end rounded-box border border-base-content/10 bg-base-200 p-3">
                    <div class="form-control md:col-span-5">
                        <label class="label">
                            <span class="inline-flex items-center gap-1">
                                Category
                                <x-ui.field-help text="Places this definition in the selected client category; enabled definitions need at least one valid association." />
                                <span class="text-error">*</span>
                            </span>
                        </label>
                        <select class="select w-full" :name="`associations[${index}][category_id]`"
                            x-model.number="association.category_id" required>
                            <option value="">Choose a category</option>
                            @foreach($categories as $id => $label)<option value="{{ $id }}">{{ $label }}</option>@endforeach
                        </select>
                    </div>
                    <div class="form-control md:col-span-2">
                        <label class="label">
                            <span class="inline-flex items-center gap-1">
                                Order
                                <x-ui.field-help text="Display order within this category; ties are ordered by achievement ID." />
                            </span>
                        </label>
                        <input type="number" min="0" max="4294967295" class="input w-full tabular-nums"
                            :name="`associations[${index}][sequence]`" x-model.number="association.sequence">
                    </div>
                    <div class="form-control md:col-span-4">
                        <label class="label">
                            <span class="inline-flex items-center gap-1">
                                Display-text override
                                <x-ui.field-help text="Optional text for this category placement; leave empty to use the achievement's normal presentation." />
                            </span>
                        </label>
                        <input class="input w-full" maxlength="255" :name="`associations[${index}][display_text]`"
                            x-model="association.display_text" placeholder="Empty uses the normal definition text">
                    </div>
                    <button type="button" class="btn btn-soft btn-error md:col-span-1" @click="remove(editor.associations, index)"
                        aria-label="Remove category association" title="Remove category association"><x-ui.icon name="delete" /></button>
                </div>
            </template>
        </div>

        <div x-show="editor.associations.length === 0" class="alert alert-error mt-3">
            An enabled definition without a valid category association prevents achievement content from loading.
        </div>
    </div>
</div>
