<div class="space-y-4">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="flex items-start justify-between gap-4 mb-4">
                <div>
                    <h2 class="card-title">
                        Definition
                        <div class="text-info text-sm">
                            ID:
                            <span x-text="editor.id"></span>
                        </div>
                    </h2>
                    <p class="text-sm opacity-65">Presentation, score, persistence version, and enable state.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <input type="hidden" name="id" x-model="editor.id" />
                <x-form.input
                    name="name"
                    label="Name"
                    tooltip="Player-facing achievement name and the default text used for achievement links."
                    maxlength="255"
                    x-model="editor.name"
                    required
                    wrapper-class="md:col-span-4"
                />
                <div class="join flex items-start gap-2">
                    <div
                        class="join-item w-10 h-10 min-w-10 border rounded border-base-content/20 bg-base-200 self-center mt-4"
                        :class="editor.icon_id ? 'item-icon item-' + editor.icon_id : ''"
                    ></div>

                    <div class="flex-1 min-w-0">
                        <label for="achievement-icon" class="label">
                            <span class="label-text">Icon</span>
                        </label>

                        <div class="flex items-center gap-2">
                            <div class="flex-1">
                                <x-form.input
                                    name="icon_id"
                                    id="achievement-icon"
                                    tooltip="Numeric client icon shown with the achievement; use 0 when no specific icon is required."
                                    type="number"
                                    min="0"
                                    x-model.number="editor.icon_id"
                                />
                            </div>

                            <div class="flex-shrink-0">
                                <button
                                    type="button"
                                    class="btn btn-soft btn-secondary h-10 w-10 p-0"
                                    @click="$store.iconPicker.open('achievement-icon')"
                                >
                                    <x-ui.icon name="search" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <x-form.input
                    name="points"
                    label="Points"
                    tooltip="Achievement score awarded when the definition is completed."
                    type="number"
                    min="0"
                    max="4294967295"
                    x-model.number="editor.points"
                />
                <x-form.textarea
                    name="description"
                    label="Description"
                    tooltip="Player-facing explanation sent to the client."
                    rows="2"
                    x-model="editor.description"
                    required
                    wrapper-class="md:col-span-6"
                />
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm mb-4">
        <div class="card-body">
            <h2 class="card-title">Options</h2>
            <div class="flex flex-wrap items-center gap-4">
                <x-form.checkbox
                    name="enabled"
                    label="Enabled"
                    tooltip="Only enabled definitions enter the active snapshot, and they must have at least one valid category association."
                    x-model="editor.enabled"
                />
            </div>
        </div>
    </div>
</div>

<div class="space-y-4">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="card bg-base-200 card-sm shadow-sm">
            <div class="card-body">
                <h2 class="card-title">Persistence Version</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-end">
                    <x-form.input
                        name="version"
                        label="Definition version"
                        tooltip="Version stored with character completion and progress. Version 0 is valid initially. Explicitly increment it when deployed criteria, rewards, selectable-source linkage, or reset semantics change."
                        type="number"
                        min="0"
                        max="4294967295"
                        x-model.number="editor.version"
                        required
                    />

                    <x-form.checkbox
                        name="reset_on_version_change"
                        label="Reset state on version mismatch"
                        tooltip="When stored and active versions differ, remove completion, progress, and reward ledgers before rebuilding character state."
                        x-model="editor.reset_on_version_change"
                    />
                </div>
                <p class="text-xs opacity-65">
                    With reset enabled, a mismatch deletes that character's completion, progress, individual reward ledger,
                    and selectable-reward ledger before state is rebuilt.
                </p>
            </div>
        </div>

        <div class="card bg-base-200 card-sm shadow-sm">
            <div class="card-body">
                <h2 class="card-title">Imported Presentation Fields</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-form.checkbox
                        name="has_reward"
                        label="Imported has-reward hint"
                        tooltip="Lossless imported client hint. Runtime derives the effective reward-button value from valid automatic and selectable reward mappings."
                        x-model="editor.has_reward"
                    />

                    <x-form.input
                        name="client_flag"
                        label="Imported client flag"
                        tooltip="Uninterpreted field 7 from AchievementsClient.txt, retained for lossless import/export. RoF2 does not receive it."
                        type="number"
                        min="0"
                        max="255"
                        x-model.number="editor.client_flag"
                    />
                </div>
            </div>
        </div>
    </div>
</div>
