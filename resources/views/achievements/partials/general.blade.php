<div class="card bg-base-100 shadow">
    <div class="card-body">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="card-title">Definition</h2>
                <p class="text-sm opacity-65">Presentation, score, persistence version, and enable state.</p>
            </div>
            <label class="label cursor-pointer gap-3">
                <span class="inline-flex items-center gap-1">
                    Enabled
                    <x-ui.field-help text="Only enabled definitions enter the active snapshot, and they must have at least one valid category association." />
                </span>
                <input type="checkbox" class="toggle toggle-success" x-model="editor.enabled">
                <input type="hidden" name="enabled" :value="editor.enabled ? 1 : 0">
            </label>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mt-2">
            <div class="form-control">
                <label class="label" for="achievement-id">
                    <span class="inline-flex items-center gap-1">
                        Stable ID
                        <x-ui.field-help text="Durable unsigned identity referenced by content and character state; it cannot be changed after creation." />
                        <span class="text-error">*</span>
                    </span>
                </label>
                <input id="achievement-id" name="id" type="number" min="1" max="4294967295" class="input w-full tabular-nums"
                    x-model.number="editor.id" @readonly(!$isCreate) required>
            </div>
            <div class="form-control md:col-span-3">
                <label class="label" for="achievement-name">
                    <span class="inline-flex items-center gap-1">
                        Name
                        <x-ui.field-help text="Player-facing achievement name and the default text used for achievement links." />
                        <span class="text-error">*</span>
                    </span>
                </label>
                <input id="achievement-name" name="name" class="input w-full" maxlength="255" x-model="editor.name" required>
            </div>
            <div class="form-control">
                <label class="label" for="achievement-icon">
                    <span class="inline-flex items-center gap-1">
                        Client icon ID
                        <x-ui.field-help text="Numeric client icon shown with the achievement; use 0 when no specific icon is required." />
                    </span>
                </label>
                <input id="achievement-icon" name="icon_id" type="number" min="0" max="4294967295" class="input w-full tabular-nums"
                    x-model.number="editor.icon_id">
            </div>
            <div class="form-control">
                <label class="label" for="achievement-points">
                    <span class="inline-flex items-center gap-1">
                        Points
                        <x-ui.field-help text="Achievement score awarded when the definition is completed." />
                    </span>
                </label>
                <input id="achievement-points" name="points" type="number" min="0" max="4294967295" class="input w-full tabular-nums"
                    x-model.number="editor.points">
            </div>
            <div class="form-control md:col-span-6">
                <label class="label" for="achievement-description">
                    <span class="inline-flex items-center gap-1">
                        Description
                        <x-ui.field-help text="Player-facing explanation sent to the client with this achievement definition." />
                        <span class="text-error">*</span>
                    </span>
                </label>
                <textarea id="achievement-description" name="description" rows="4" class="textarea w-full"
                    x-model="editor.description" required></textarea>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4">
    <div class="card bg-base-100 shadow">
        <div class="card-body">
            <h2 class="card-title">Persistence Version</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-end">
                <div class="form-control">
                    <label class="label" for="definition-version">
                        <span class="inline-flex items-center gap-1">
                            Definition version
                            <x-ui.field-help text="Nonzero version stored with character completion and progress; increment it for incompatible deployed changes." />
                        </span>
                    </label>
                    <input id="definition-version" name="definition_version" type="number" min="1" max="4294967295"
                        class="input w-full tabular-nums" x-model.number="editor.definition_version" required>
                </div>
                <label class="label cursor-pointer justify-start gap-3 min-h-12">
                    <input type="checkbox" class="checkbox checkbox-warning" x-model="editor.reset_on_version_change">
                    <input type="hidden" name="reset_on_version_change" :value="editor.reset_on_version_change ? 1 : 0">
                    <span class="inline-flex items-center gap-1">
                        Reset state on version mismatch
                        <x-ui.field-help text="When stored and active versions differ, remove completion, progress, and reward ledgers before rebuilding character state." />
                    </span>
                </label>
            </div>
            <p class="text-xs opacity-65">
                With reset enabled, a mismatch deletes that character's completion, progress, individual reward ledger,
                and selectable-reward ledger before state is rebuilt.
            </p>
        </div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body">
            <h2 class="card-title">Imported Presentation Fields</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="form-control">
                    <label class="label" for="reward-display">
                        <span class="inline-flex items-center gap-1">
                            Imported reward display
                            <x-ui.field-help text="Imported client presentation value; runtime derives the active reward display from valid server-authored rewards." />
                        </span>
                    </label>
                    <input id="reward-display" name="reward_display" type="number" min="0" max="4294967295"
                        class="input w-full tabular-nums" x-model.number="editor.reward_display">
                    <p class="text-xs opacity-60 mt-1">Runtime replaces this with 1 only when valid server-authored rewards exist.</p>
                </div>
                <div class="form-control">
                    <label class="label" for="world-display">
                        <span class="inline-flex items-center gap-1">
                            World display flag
                            <x-ui.field-help text="Newer-client styling value retained with imported data; RoF2 does not receive this field." />
                        </span>
                    </label>
                    <input id="world-display" name="world_display_flag" type="number" min="0" max="255"
                        class="input w-full tabular-nums" x-model.number="editor.world_display_flag">
                    <p class="text-xs opacity-60 mt-1">Retained for provenance; RoF2 does not receive it.</p>
                </div>
            </div>
        </div>
    </div>
</div>
