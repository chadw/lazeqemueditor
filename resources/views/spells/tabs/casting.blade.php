<div class="space-y-6">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                <x-form.progress-input
                    name="cast_time"
                    label="Cast Time"
                    tooltip="Time it takes to cast the spell."
                    :value="$spell->cast_time"
                />
                <x-form.progress-input
                    name="recovery_time"
                    label="Recovery Time"
                    tooltip="Sets the global recast delay on all spell gems."
                    :value="$spell->recovery_time"
                />
                <x-form.progress-input
                    name="recast_time"
                    label="Recast Time"
                    tooltip="Sets recast delay on the spell gem used in casting."
                    :value="$spell->recast_time"
                />
                <x-form.input
                    name="basediff"
                    label="Fizzle Adjustment"
                    tooltip="Fizzle rate modifier, positive values increase fizzle chance, lower values decrease it."
                    :value="$spell->basediff"
                />
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                <div
                    x-data='ajaxSelect({
                        searchUrl: "/items/search",
                        useModal: false,
                        prefillValue: @json($spell->comp1),
                        allowNone: true,
                        noneId: -1,
                    })'
                    x-init="init()"
                >
                    <label class="label">Components #1</label>
                    <select
                        x-ref="select"
                        name="components1"
                        class="w-full"
                        tooltip="Item ID cost required to cast this spell"
                    ></select>
                </div>
                <div
                    x-data='ajaxSelect({
                        searchUrl: "/items/search",
                        useModal: false,
                        prefillValue: @json($spell->comp2),
                        allowNone: true,
                        noneId: -1,
                    })'
                    x-init="init()"
                >
                    <label class="label">Components #2</label>
                    <select
                        x-ref="select"
                        name="components2"
                        class="w-full"
                        tooltip="Item ID cost required to cast this spell"
                    ></select>
                </div>
                <div
                    x-data='ajaxSelect({
                        searchUrl: "/items/search",
                        useModal: false,
                        prefillValue: @json($spell->comp3),
                        allowNone: true,
                        noneId: -1,
                    })'
                    x-init="init()"
                >
                    <label class="label">Components #3</label>
                    <select
                        x-ref="select"
                        name="components3"
                        class="w-full"
                        tooltip="Item ID cost required to cast this spell"
                    ></select>
                </div>
                <div
                    x-data='ajaxSelect({
                        searchUrl: "/items/search",
                        useModal: false,
                        prefillValue: @json($spell->comp4),
                        allowNone: true,
                        noneId: -1,
                    })'
                    x-init="init()"
                >
                    <label class="label">Components #4</label>
                    <select
                        x-ref="select"
                        name="components4"
                        class="w-full"
                        tooltip="Item ID cost required to cast this spell"
                    ></select>
                </div>
                <x-form.input
                    name="component_counts1"
                    label="Components Count #1"
                    tooltip="Item count for component required to cast this spell"
                    :value="$spell->component_counts1"
                    type="number"
                    min="-1"
                />
                <x-form.input
                    name="component_counts2"
                    label="Components Count #2"
                    tooltip="Item count for component required to cast this spell"
                    :value="$spell->component_counts2"
                    type="number"
                    min="-1"
                />
                <x-form.input
                    name="component_counts3"
                    label="Components Count #3"
                    tooltip="Item count for component required to cast this spell"
                    :value="$spell->component_counts3"
                    type="number"
                    min="-1"
                />
                <x-form.input
                    name="component_counts4"
                    label="Components Count #4"
                    tooltip="Item count for component required to cast this spell"
                    :value="$spell->component_counts4"
                    type="number"
                    min="-1"
                />
                <div
                    x-data='ajaxSelect({
                        searchUrl: "/items/search",
                        useModal: false,
                        prefillValue: @json($spell->reagent1),
                        allowNone: true,
                        noneId: -1,
                        seedOptions: { 1: "1: 1", 2: "2: 2", 3: "3: 3", 4: "4: 4" }
                    })'
                    x-init="init()"
                >
                    <label class="label">Reagent #1</label>
                    <select
                        x-ref="select"
                        name="NoexpendReagent1"
                        class="w-full"
                        tooltip="Item ID that needs to exist in inventory to cast this spell"
                    ></select>
                </div>
                <div
                    x-data='ajaxSelect({
                        searchUrl: "/items/search",
                        useModal: false,
                        prefillValue: @json($spell->reagent2),
                        allowNone: true,
                        noneId: -1,
                        seedOptions: { 1: "1: 1", 2: "2: 2", 3: "3: 3", 4: "4: 4" }
                    })'
                    x-init="init()"
                >
                    <label class="label">Reagent #2</label>
                    <select
                        x-ref="select"
                        name="NoexpendReagent2"
                        class="w-full"
                        tooltip="Item ID that needs to exist in inventory to cast this spell"
                    ></select>
                </div>
                <div
                    x-data='ajaxSelect({
                        searchUrl: "/items/search",
                        useModal: false,
                        prefillValue: @json($spell->reagent3),
                        allowNone: true,
                        noneId: -1,
                        seedOptions: { 1: "1: 1", 2: "2: 2", 3: "3: 3", 4: "4: 4" }
                    })'
                    x-init="init()"
                >
                    <label class="label">Reagent #3</label>
                    <select
                        x-ref="select"
                        name="NoexpendReagent3"
                        class="w-full"
                        tooltip="Item ID that needs to exist in inventory to cast this spell"
                    ></select>
                </div>
                <div
                    x-data='ajaxSelect({
                        searchUrl: "/items/search",
                        useModal: false,
                        prefillValue: @json($spell->reagent4),
                        allowNone: true,
                        noneId: -1,
                        seedOptions: { 1: "1: 1", 2: "2: 2", 3: "3: 3", 4: "4: 4" }
                    })'
                    x-init="init()"
                >
                    <label class="label">Reagent #4</label>
                    <select
                        x-ref="select"
                        name="NoexpendReagent4"
                        class="w-full"
                        tooltip="Item ID that needs to exist in inventory to cast this spell"
                    ></select>
                </div>
                <div class="col-span-4">
                    <strong>Regents:</strong>
                    If it is a number between 1-4 it means component number 1-4 is a focus and not to expend it.
                    If it is a valid item ID it means this item is a focus as well.
                </div>
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Options</h2>
            <div class="flex flex-wrap items-center gap-4">
                <x-form.checkbox
                    name="uninterruptable"
                    label="Uninterruptable"
                    tooltip="Spell can not be interrupted."
                    :checked="$spell->uninterruptable"
                />
                <x-form.checkbox
                    name="cast_not_standing"
                    label="Cast Not Standing"
                    tooltip="Can cast from sitting position, can cast while invisible, ignores invulnerability, can not be interrupted by SPA 343 SE_InterruptCasting."
                    :checked="$spell->cast_not_standing"
                />
            </div>
        </div>
    </div>
</div>
