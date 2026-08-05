<div class="space-y-4">
    <div class="card bg-base-100 shadow">
        <div class="card-body flex-row items-start justify-between gap-4">
            <div>
                <h2 class="card-title">Components and Criteria</h2>
                <p class="text-sm opacity-65">
                    Components define the client rows. Criteria define how each state-bearing row is evaluated.
                    Multiple enabled criteria on one component are alternatives and must use one identical policy.
                </p>
            </div>
            <button type="button" class="btn btn-sm btn-soft btn-success shrink-0" @click="addComponent()"
                title="Add a new client component row with a globally unused suggested component ID">
                <x-ui.icon name="add" /> Add Component
            </button>
        </div>
    </div>

    <template x-for="(component, componentIndex) in editor.components" :key="component._uid">
        <div class="card bg-base-100 shadow border border-base-content/10">
            <div class="card-body">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h3 class="card-title text-base">
                            Component <span class="tabular-nums" x-text="component.component_id"></span>
                        </h3>
                        <p class="text-xs opacity-60" x-text="`${component.criteria.length} criterion row${component.criteria.length === 1 ? '' : 's'}`"></p>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" class="btn btn-sm btn-soft btn-success" @click="addCriterion(component)"
                            title="Add an alternative server evaluation rule to this component"
                            :disabled="Number(component.component_type) === 3">
                            <x-ui.icon name="add" /> Add Criterion
                        </button>
                        <button type="button" class="btn btn-sm btn-soft btn-error"
                            @click="if (confirm('Remove this component and all of its criteria? Existing character progress for its identity is retained as history but will no longer be loaded.')) remove(editor.components, componentIndex)"
                            title="Remove this component and its criteria from the definition">
                            <x-ui.icon name="delete" /> Remove
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 mt-2">
                    <div class="form-control md:col-span-2">
                        <label class="label">
                            <span>Wire type <x-ui.field-help text="RoF2 client component bucket. Types 0–2 may carry state; type 3 is display-only." /></span>
                        </label>
                        <select class="select w-full" :name="`components[${componentIndex}][component_type]`"
                            x-model.number="component.component_type" required>
                            @foreach($metadata['component_types'] as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-control md:col-span-2">
                        <label class="label">
                            <span>Component ID <x-ui.field-help text="Stable identity within the component type. Character progress and shared presentation counts reference this value." /></span>
                        </label>
                        <input type="number" min="0" max="4294967295" class="input w-full tabular-nums"
                            :name="`components[${componentIndex}][component_id]`" x-model.number="component.component_id" required>
                    </div>
                    <div class="form-control md:col-span-2">
                        <label class="label">
                            <span>Client order <x-ui.field-help text="Ascending display order for components in this achievement." /></span>
                        </label>
                        <input type="number" min="0" max="4294967295" class="input w-full tabular-nums"
                            :name="`components[${componentIndex}][sequence]`" x-model.number="component.sequence" required>
                    </div>
                    <div class="form-control md:col-span-2">
                        <label class="label">
                            <span>Presentation count <x-ui.field-help text="Count shown to the client. This is global for a component ID, so reused IDs must use the same value." /></span>
                        </label>
                        <input type="number" min="1" max="4294967295" class="input w-full tabular-nums"
                            :name="`components[${componentIndex}][presentation_count]`" x-model.number="component.presentation_count" required>
                    </div>
                    <div class="form-control md:col-span-4">
                        <label class="label">
                            <span>Primary description <x-ui.field-help text="Main client-facing explanation of this component's objective." /></span>
                        </label>
                        <input class="input w-full" :name="`components[${componentIndex}][description]`"
                            x-model="component.description">
                    </div>
                    <div class="form-control md:col-span-12">
                        <label class="label">
                            <span>Secondary description <x-ui.field-help text="Optional second client-facing line for extra objective detail." /></span>
                        </label>
                        <input class="input w-full" :name="`components[${componentIndex}][description_2]`"
                            x-model="component.description_2">
                    </div>
                </div>

                <div x-show="Number(component.component_type) === 3" class="alert alert-info py-2 mt-2">
                    Type 3 is presentation-only. It may be displayed by the client but cannot have an enabled criterion.
                </div>

                <div class="space-y-3 mt-3">
                    <template x-for="(criterion, criterionIndex) in component.criteria" :key="criterion._uid">
                        <div class="rounded-box border border-base-content/10 bg-base-200 p-4 space-y-3">
                            <input type="hidden" :name="`components[${componentIndex}][criteria][${criterionIndex}][id]`"
                                :value="criterion.id ?? ''">

                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div class="font-semibold">
                                    Criterion <span x-text="criterionIndex + 1"></span>
                                    <span class="badge badge-sm ml-1" x-text="criterion.id ? `row ${criterion.id}` : 'new'"></span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <label class="label cursor-pointer gap-2 py-0">
                                        <span class="text-sm">Enabled <x-ui.field-help text="Disabled criteria remain authored but are ignored by runtime evaluation." /></span>
                                        <input type="checkbox" class="toggle toggle-sm toggle-success" x-model="criterion.enabled">
                                        <input type="hidden"
                                            :name="`components[${componentIndex}][criteria][${criterionIndex}][enabled]`"
                                            :value="criterion.enabled ? 1 : 0">
                                    </label>
                                    <button type="button" class="btn btn-xs btn-soft btn-error"
                                        @click="if (!criterion.id || confirm('Remove this deployed criterion? This can change completion, unlock, visibility, or blocker behavior after reload.')) remove(component.criteria, criterionIndex)"
                                        title="Remove this criterion from runtime evaluation">
                                        <x-ui.icon name="delete" />
                                    </button>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                                <div class="form-control md:col-span-4">
                                    <label class="label">
                                        <span>Event <x-ui.field-help text="Server event or durable state source that evaluates this criterion. Help below changes with this selection." /></span>
                                    </label>
                                    <select class="select w-full"
                                        :name="`components[${componentIndex}][criteria][${criterionIndex}][event_type]`"
                                        x-model.number="criterion.event_type" @change="applyEventDefaults(criterion)" required>
                                        @foreach($metadata['event_types'] as $value => $label)
                                            <option value="{{ $value }}">{{ $value }} — {{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-control md:col-span-3">
                                    <label class="label">
                                        <span>Progress mode <x-ui.field-help text="Increment adds credit, Highest keeps the maximum, Set replaces progress, and Boolean evaluates a threshold." /></span>
                                    </label>
                                    <select class="select w-full"
                                        :name="`components[${componentIndex}][criteria][${criterionIndex}][progress_mode]`"
                                        x-model.number="criterion.progress_mode" required>
                                        @foreach($metadata['progress_modes'] as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-control md:col-span-3">
                                    <label class="label">
                                        <span>Behavior <x-ui.field-help text="Controls completion, unlock, visibility, display, or blocker semantics. Required criteria drive normal completion." /></span>
                                    </label>
                                    <select class="select w-full"
                                        :name="`components[${componentIndex}][criteria][${criterionIndex}][behavior]`"
                                        x-model.number="criterion.behavior" required>
                                        @foreach($metadata['behaviors'] as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-control md:col-span-2">
                                    <label class="label">
                                        <span>Required count <x-ui.field-help text="Progress required for this component policy to be satisfied. Alternatives on the same component must match." /></span>
                                    </label>
                                    <input type="number" min="1" max="4294967295" class="input w-full tabular-nums"
                                        :name="`components[${componentIndex}][criteria][${criterionIndex}][required_count]`"
                                        x-model.number="criterion.required_count" required>
                                </div>
                            </div>

                            <div class="alert alert-info py-2 text-sm" x-text="eventHint(criterion.event_type)"></div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div class="form-control">
                                    <label class="label justify-start gap-1">
                                        <span x-text="targetLabel(criterion.event_type)"></span>
                                        <span class="tooltip tooltip-right" tabindex="0" role="note"
                                            :data-tip="targetHelp(criterion.event_type)"
                                            :aria-label="targetHelp(criterion.event_type)">
                                            <span class="badge badge-ghost badge-xs cursor-help">?</span>
                                        </span>
                                    </label>

                                    <template x-if="Number(criterion.event_type) === 3">
                                        <select class="select w-full"
                                            :name="`components[${componentIndex}][criteria][${criterionIndex}][target_id]`"
                                            x-model.number="criterion.target_id">
                                            @foreach($raceOptions as $id => $label)<option value="{{ $id }}">{{ $id }} — {{ $label }}</option>@endforeach
                                        </select>
                                    </template>

                                    <template x-if="[9, 13].includes(Number(criterion.event_type))">
                                        <select class="select w-full"
                                            :name="`components[${componentIndex}][criteria][${criterionIndex}][target_id]`"
                                            x-model.number="criterion.target_id">
                                            <template x-if="Number(criterion.event_type) === 9">
                                                <option value="4294967295">4294967295 — Any skill</option>
                                            </template>
                                            @foreach($skillOptions as $id => $label)<option value="{{ $id }}">{{ $id }} — {{ $label }}</option>@endforeach
                                        </select>
                                    </template>

                                    <template x-if="Number(criterion.event_type) === 12">
                                        <div class="space-y-2">
                                            <input type="number" min="1" max="4294967295" class="input w-full tabular-nums"
                                                :name="`components[${componentIndex}][criteria][${criterionIndex}][target_id]`"
                                                x-model.number="criterion.target_id" required>
                                            <div class="join w-full">
                                                <input class="input input-sm join-item w-full" x-model="criterion.target_name"
                                                    placeholder="NPC name to canonicalize">
                                                <button type="button" class="btn btn-sm join-item" @click="applyNpcName(criterion)"
                                                    title="Canonicalize the entered ASCII NPC name and calculate EQEmu's unsigned FNV-1a identity">Hash</button>
                                            </div>
                                            <p class="text-xs opacity-60">
                                                Canonical: <code x-text="canonicalNpcName(criterion.target_name) || '—'"></code>
                                            </p>
                                        </div>
                                    </template>

                                    <template x-for="lookup in [lookupType(criterion.event_type)]"
                                        :key="`${criterion._uid}-target-${lookup}`">
                                        <template x-if="lookup">
                                            <div x-data="ajaxSelect({
                                                    searchUrl: `/achievements/lookups/${lookup}`,
                                                    prefillPath: `/achievements/lookups/${lookup}`,
                                                    prefillValue: () => Number(criterion.target_id) || null,
                                                    allowNone: ![4].includes(Number(criterion.event_type)),
                                                    noneId: 0,
                                                    noneLabel: '0: Any / wildcard',
                                                    useModal: false,
                                                })" x-init="init()">
                                                <select x-ref="select" class="w-full"
                                                    :name="`components[${componentIndex}][criteria][${criterionIndex}][target_id]`"
                                                    x-model.number="criterion.target_id"></select>
                                            </div>
                                        </template>
                                    </template>

                                    <template x-if="!lookupType(criterion.event_type) && ![3, 9, 12, 13].includes(Number(criterion.event_type))">
                                        <input type="number" min="0" max="4294967295" class="input w-full tabular-nums"
                                            :name="`components[${componentIndex}][criteria][${criterionIndex}][target_id]`"
                                            x-model.number="criterion.target_id" required>
                                    </template>
                                </div>

                                <div class="form-control">
                                    <label class="label justify-start gap-1">
                                        <span x-text="targetLabel(criterion.event_type, 2)"></span>
                                        <span class="tooltip tooltip-right" tabindex="0" role="note"
                                            :data-tip="targetHelp(criterion.event_type, 2)"
                                            :aria-label="targetHelp(criterion.event_type, 2)">
                                            <span class="badge badge-ghost badge-xs cursor-help">?</span>
                                        </span>
                                    </label>
                                    <template x-if="[7, 13].includes(Number(criterion.event_type))">
                                        <select class="select w-full"
                                            :name="`components[${componentIndex}][criteria][${criterionIndex}][target_id2]`"
                                            x-model.number="criterion.target_id2">
                                            @foreach($classOptions as $id => $label)<option value="{{ $id }}">{{ $id }} — {{ $label }}</option>@endforeach
                                        </select>
                                    </template>
                                    <template x-if="Number(criterion.event_type) === 12">
                                        <div x-data="ajaxSelect({
                                                searchUrl: '/achievements/lookups/zone',
                                                prefillPath: '/achievements/lookups/zone',
                                                prefillValue: () => Number(criterion.target_id2) || null,
                                                allowNone: true,
                                                noneId: 0,
                                                noneLabel: '0: Any zone',
                                                useModal: false,
                                            })" x-init="init()">
                                            <select x-ref="select" class="w-full"
                                                :name="`components[${componentIndex}][criteria][${criterionIndex}][target_id2]`"
                                                x-model.number="criterion.target_id2"></select>
                                        </div>
                                    </template>
                                    <template x-if="![7, 12, 13].includes(Number(criterion.event_type))">
                                        <input type="number" min="0" max="0" class="input w-full tabular-nums"
                                            :name="`components[${componentIndex}][criteria][${criterionIndex}][target_id2]`"
                                            x-model.number="criterion.target_id2" readonly>
                                    </template>
                                </div>

                                <div class="form-control">
                                    <label class="label justify-start gap-1">
                                        <span x-text="targetValueLabel(criterion.event_type)"></span>
                                        <span class="tooltip tooltip-left" tabindex="0" role="note"
                                            :data-tip="targetValueHelp(criterion.event_type)"
                                            :aria-label="targetValueHelp(criterion.event_type)">
                                            <span class="badge badge-ghost badge-xs cursor-help">?</span>
                                        </span>
                                    </label>
                                    <input type="number" min="0" class="input w-full tabular-nums"
                                        :max="Number(criterion.event_type) === 13 ? 255 : null"
                                        :name="`components[${componentIndex}][criteria][${criterionIndex}][target_value]`"
                                        x-model="criterion.target_value" required>
                                    <p class="text-xs opacity-60 mt-1">
                                        Boolean mode compares the observed value to this threshold; other modes generally use it as a minimum event filter.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div x-show="component.criteria.length === 0" class="rounded-box border border-dashed border-base-content/20 p-4 text-sm opacity-65 text-center">
                    No criteria. The component is presentation-only until a criterion is added.
                </div>
            </div>
        </div>
    </template>

    <div x-show="editor.components.length === 0" class="alert alert-warning">
        This achievement has no component graph. Add a component before enabling it for ordinary runtime completion.
    </div>
</div>
