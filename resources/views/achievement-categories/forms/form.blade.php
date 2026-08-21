<div class="space-y-6" x-data="formTracker">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-6 gap-4">
                <input type="hidden" name="id" x-model="$store.modalForm.form.id" />
                <x-form.select
                    name="parent_id"
                    label="Parent"
                    :options="$parentOptions"
                    keyInOption="true"
                    id="modal-achievement-category-parent"
                    x-model="$store.modalForm.form.parent_id"
                    required
                    help="Choose Root for parent ID 0, or place the category beneath an existing cycle-free parent."
                />
                <x-form.input
                    name="sequence"
                    label="Sibling order"
                    type="number"
                    min="0"
                    max="4294967295"
                    x-model="$store.modalForm.form.sequence"
                    help="Sort order among categories with the same parent; ties are ordered by category ID."
                />
                <x-form.input
                    name="name"
                    label="Name"
                    x-model="$store.modalForm.form.name"
                    wrapper-class="col-span-3"
                    help="Player-facing label shown in the achievement category tree."
                    required
                />
                <x-form.input
                    name="icon"
                    label="Client texture/resource"
                    keyInOption="true"
                    x-model="$store.modalForm.form.icon"
                    help="Optional client texture or resource name, such as A_Hunter; empty produces text-only presentation."
                />
            </div>
        </div>
    </div>
    <div class="card bg-base-200 shadow-sm">
        <div class="card-body">
            <x-form.textarea
                name="description"
                label="Description"
                x-model="$store.modalForm.form.description"
                rows="2"
                help="Description sent to the client for this category."
            />
        </div>
    </div>
</div>
