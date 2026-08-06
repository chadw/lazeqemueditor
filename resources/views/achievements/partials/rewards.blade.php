<div class="space-y-4">
    <div class="card bg-base-100 shadow">
        <div class="card-body">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h2 class="card-title">Reward Grants</h2>
                    <p class="text-sm opacity-65">
                        Unmapped rows grant automatically. Mapping a row to any option suppresses automatic delivery,
                        even if the option or set is disabled.
                    </p>
                </div>
                <button type="button" class="btn btn-sm btn-soft btn-success" @click="addReward()"
                    title="Add a new independently ledgered server reward grant">
                    <x-ui.icon name="add" /> Add Grant
                </button>
            </div>

            <div class="space-y-3 mt-3">
                <template x-for="(reward, rewardIndex) in editor.rewards" :key="reward._uid">
                    <div class="rounded-box border border-base-content/10 bg-base-200 p-4">
                        <input type="hidden" :name="`rewards[${rewardIndex}][reward_id]`" :value="reward.reward_id ?? ''">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                            <div class="form-control md:col-span-2">
                                <label class="label">
                                    <span>Type <x-ui.field-help text="Kind of server-authored grant. The referenced-data meaning changes with this selection." /></span>
                                </label>
                                <select class="select w-full" :name="`rewards[${rewardIndex}][reward_type]`"
                                    x-model.number="reward.reward_type" @change="applyRewardTypeDefaults(reward)" required>
                                    @foreach($metadata['reward_types'] as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-control md:col-span-3">
                                <label class="label justify-start gap-1">
                                    <span x-text="rewardLookupType(reward.reward_type) ? 'Referenced data' : (Number(reward.reward_type) === 1 ? 'XP mode' : 'Data (normally 0)')"></span>
                                    <span class="tooltip tooltip-right" tabindex="0" role="note"
                                        :data-tip="rewardDataHelp(reward.reward_type)"
                                        :aria-label="rewardDataHelp(reward.reward_type)">
                                        <span class="badge badge-ghost badge-xs cursor-help">?</span>
                                    </span>
                                </label>
                                <template x-for="lookup in [rewardLookupType(reward.reward_type)]"
                                    :key="`${reward._uid}-data-${lookup}`">
                                    <template x-if="lookup">
                                        <div x-data="ajaxSelect({
                                                searchUrl: `/achievements/lookups/${lookup}`,
                                                prefillPath: `/achievements/lookups/${lookup}`,
                                                prefillValue: () => Number(reward.reward_data_id) || null,
                                                useModal: false,
                                            })" x-init="init()">
                                            <select x-ref="select" class="w-full" :name="`rewards[${rewardIndex}][reward_data_id]`"
                                                x-model.number="reward.reward_data_id"></select>
                                        </div>
                                    </template>
                                </template>
                                <template x-if="Number(reward.reward_type) === 1">
                                    <select class="select w-full" :name="`rewards[${rewardIndex}][reward_data_id]`"
                                        x-model.number="reward.reward_data_id">
                                        <option value="0">0 — Normal XP handling</option>
                                        <option value="1">1 — Normal-only raw XP</option>
                                    </select>
                                </template>
                                <template x-if="![0, 1, 4, 5].includes(Number(reward.reward_type))">
                                    <input type="number" min="0" max="4294967295" class="input w-full tabular-nums"
                                        :name="`rewards[${rewardIndex}][reward_data_id]`" x-model.number="reward.reward_data_id" required>
                                </template>
                            </div>

                            <div class="form-control md:col-span-2">
                                <label class="label">
                                    <span>Amount <x-ui.field-help text="Positive quantity granted. For item rewards this is the stack count; for currency, XP, and AA it is the awarded amount." /></span>
                                </label>
                                <input type="number" min="1" class="input w-full tabular-nums"
                                    :name="`rewards[${rewardIndex}][amount]`" x-model="reward.amount" required>
                            </div>
                            <div class="form-control md:col-span-1">
                                <label class="label">
                                    <span>Order <x-ui.field-help text="Unique sequence controlling deterministic reward display and delivery order within this achievement." /></span>
                                </label>
                                <input type="number" min="0" max="4294967295" class="input w-full tabular-nums"
                                    :name="`rewards[${rewardIndex}][sequence]`" x-model.number="reward.sequence" required>
                            </div>
                            <div class="form-control md:col-span-3">
                                <label class="label">
                                    <span>Delivery <x-ui.field-help text="Automatic grants run at completion. Selecting an option maps this row to selectable delivery and always suppresses automatic delivery." /></span>
                                </label>
                                <select class="select w-full" x-model.number="reward.option_id">
                                    <option value=""
                                        :selected="reward.option_id === null || reward.option_id === ''">
                                        Automatic on completion
                                    </option>
                                    <template x-for="option in editor.reward_set.options" :key="option._uid">
                                        <option :value="option.option_id"
                                            :selected="String(reward.option_id ?? '') === String(option.option_id)"
                                            x-text="optionLabel(option)"></option>
                                    </template>
                                </select>
                                <input type="hidden" :name="`rewards[${rewardIndex}][option_id]`"
                                    :value="reward.option_id ?? ''">
                            </div>
                            <div class="md:col-span-1 flex justify-end">
                                <button type="button" class="btn btn-soft btn-error"
                                    @click="if (!reward.reward_id || confirm('Remove this deployed reward grant? Existing character reward ledgers are preserved, but future completions will no longer deliver it.')) remove(editor.rewards, rewardIndex)"
                                    aria-label="Remove reward"><x-ui.icon name="delete" /></button>
                            </div>
                            <div class="form-control md:col-span-10">
                                <label class="label">
                                    <span>Client description <x-ui.field-help text="Short player-facing text describing this grant in reward UI." /></span>
                                </label>
                                <input class="input w-full" maxlength="255" :name="`rewards[${rewardIndex}][description]`"
                                    x-model="reward.description">
                            </div>
                            <label class="label cursor-pointer justify-start gap-3 md:col-span-2 min-h-12">
                                <input type="checkbox" class="toggle toggle-sm toggle-success" x-model="reward.enabled">
                                <input type="hidden" :name="`rewards[${rewardIndex}][enabled]`" :value="reward.enabled ? 1 : 0">
                                <span>Enabled <x-ui.field-help text="Disabled grants stay authored but are never delivered. Existing durable ledgers are not erased." /></span>
                            </label>
                        </div>
                    </div>
                </template>
            </div>

            <div x-show="editor.rewards.length === 0" class="rounded-box border border-dashed border-base-content/20 p-4 mt-3 text-center text-sm opacity-65">
                No server-authored reward grants are configured.
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h2 class="card-title">Selectable Reward Set</h2>
                    <p class="text-sm opacity-65">Common rows are combined with exactly one enabled non-common choice.</p>
                </div>
                <label class="label cursor-pointer gap-3">
                    <span>Use selectable rewards <x-ui.field-help text="Enables a choose-one reward graph with optional common grants. Turning this off removes the set when saved." /></span>
                    <input type="checkbox" class="toggle toggle-success" :checked="editor.reward_set.present"
                        @change="
                            if ($event.target.checked) {
                                editor.reward_set.present = true;
                            } else if (confirm('Remove the selectable reward set and all of its option mappings when this definition is saved? Character selection ledgers are preserved as history, and mapped grants must be reviewed before save.')) {
                                editor.reward_set.present = false;
                            } else {
                                $event.target.checked = true;
                            }
                        ">
                </label>
            </div>

            <input type="hidden" name="has_reward_set" :value="editor.reward_set.present ? 1 : 0">
            <input type="hidden" name="reward_set[present]" :value="editor.reward_set.present ? 1 : 0">

            <div x-show="editor.reward_set.present" x-cloak class="space-y-4 mt-3">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                    <div class="form-control md:col-span-2">
                        <label class="label">
                            <span>Stable set ID <x-ui.field-help text="Durable reward-set identity referenced by character selection ledgers. Do not reuse or change casually after deployment." /></span>
                        </label>
                        <input type="number" min="1" max="4294967295" class="input w-full tabular-nums"
                            name="reward_set[reward_set_id]" x-model.number="editor.reward_set.reward_set_id" required>
                    </div>
                    <div class="form-control md:col-span-7">
                        <label class="label">
                            <span>Prompt / title <x-ui.field-help text="Player-facing heading shown when the client asks the character to choose a reward." /></span>
                        </label>
                        <input class="input w-full" maxlength="255" name="reward_set[title]" x-model="editor.reward_set.title">
                    </div>
                    <label class="label cursor-pointer justify-start gap-3 md:col-span-3 min-h-12">
                        <input type="checkbox" class="toggle toggle-success" x-model="editor.reward_set.enabled">
                        <input type="hidden" name="reward_set[enabled]" :value="editor.reward_set.enabled ? 1 : 0">
                        <span>Set enabled <x-ui.field-help text="Only enabled sets may be selected. An enabled set requires at least one enabled non-common option." /></span>
                    </label>
                </div>

                <div class="flex items-center justify-between gap-3">
                    <h3 class="font-semibold">Options</h3>
                    <button type="button" class="btn btn-sm btn-soft btn-success" @click="addRewardOption()"
                        title="Add a common group or player-selectable reward option">
                        <x-ui.icon name="add" /> Add Option
                    </button>
                </div>

                <div class="space-y-3">
                    <template x-for="(option, optionIndex) in editor.reward_set.options" :key="option._uid">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end rounded-box border border-base-content/10 bg-base-200 p-3">
                            <div class="form-control md:col-span-2">
                                <label class="label">
                                    <span>Option ID <x-ui.field-help text="Stable option identity used by reward mappings and character selection ledgers." /></span>
                                </label>
                                <input type="number" min="1" max="4294967295" class="input w-full tabular-nums"
                                    :name="`reward_set[options][${optionIndex}][option_id]`" x-model.number="option.option_id" required>
                            </div>
                            <div class="form-control md:col-span-1">
                                <label class="label">
                                    <span>Order <x-ui.field-help text="Ascending client display order for reward choices." /></span>
                                </label>
                                <input type="number" min="0" max="4294967295" class="input w-full tabular-nums"
                                    :name="`reward_set[options][${optionIndex}][sequence]`" x-model.number="option.sequence" required>
                            </div>
                            <div class="form-control md:col-span-5">
                                <label class="label">
                                    <span>Label <x-ui.field-help text="Player-facing name for this common group or selectable choice." /></span>
                                </label>
                                <input class="input w-full" maxlength="255"
                                    :name="`reward_set[options][${optionIndex}][label]`" x-model="option.label">
                            </div>
                            <div class="form-control md:col-span-1">
                                <label class="label">
                                    <span>Flags <x-ui.field-help text="Raw RoF2 option flag byte. Leave 0 unless client behavior requires a documented flag." /></span>
                                </label>
                                <input type="number" min="0" max="255" class="input w-full tabular-nums"
                                    :name="`reward_set[options][${optionIndex}][flags]`" x-model.number="option.flags" required>
                            </div>
                            <div class="md:col-span-2 flex flex-wrap gap-3 items-center min-h-12">
                                <label class="label cursor-pointer gap-2 py-0">
                                    <input type="checkbox" class="checkbox checkbox-sm" x-model="option.common_to_all">
                                    <input type="hidden" :name="`reward_set[options][${optionIndex}][common_to_all]`"
                                        :value="option.common_to_all ? 1 : 0">
                                    <span>Common <x-ui.field-help text="Common options are granted alongside the selected non-common choice; they are not choices themselves." /></span>
                                </label>
                                <label class="label cursor-pointer gap-2 py-0">
                                    <input type="checkbox" class="checkbox checkbox-sm checkbox-success" x-model="option.enabled">
                                    <input type="hidden" :name="`reward_set[options][${optionIndex}][enabled]`"
                                        :value="option.enabled ? 1 : 0">
                                    <span>Enabled <x-ui.field-help text="Disabled options cannot be selected or granted, but their authored mappings remain durable." /></span>
                                </label>
                            </div>
                            <button type="button" class="btn btn-soft btn-error md:col-span-1"
                                @click="if (confirm('Remove this option? Its mapped grants will be disabled and changed to automatic delivery so they cannot be granted unexpectedly. Review them before re-enabling.')) removeRewardOption(optionIndex)"
                                aria-label="Remove reward option"><x-ui.icon name="delete" /></button>
                        </div>
                    </template>
                </div>

                <div x-show="editor.reward_set.options.length === 0" class="alert alert-soft alert-warning">
                    An enabled selectable set requires at least one enabled choice option and a mapped enabled grant for every enabled option.
                </div>
            </div>
        </div>
    </div>
</div>
