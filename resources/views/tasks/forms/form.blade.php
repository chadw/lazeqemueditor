<div class="space-y-6">
    <div class="card bg-base-200 shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Task Details</h2>
            <div class="grid grid-cols-6 gap-4 mb-2">
                <x-form.input
                    name="title"
                    label="Title"
                    :value="$task->title"
                    wrapper-class="col-span-3"
                    required
                    x-model="task.title"
                />
                <x-form.select
                    name="type"
                    label="Type"
                    :options="config('everquest.task_type')"
                    :selected="$task->type"
                    x-model="task.type"
                />
                <x-form.select
                    name="duration_code"
                    label="Duration Code"
                    :options="config('everquest.task_duration')"
                    tooltip="Reflects the type of duration"
                    :selected="$task->duration_code"
                />
                <div x-data="durationHelper()">
                    <x-form.input
                        name="duration"
                        label="Duration"
                        type="number"
                        min="0"
                        :value="$task->duration"
                        x-model.number="seconds"
                        x-bind:label-suffix="true"
                        x-model="task.duration"
                    />
                </div>
                <x-form.textarea
                    name="description"
                    label="Description"
                    rows="6"
                    :value="$task->description"
                    wrapper-class="col-span-6"
                    x-model="task.description"
                />
            </div>
            <div class="grid grid-cols-8 gap-4">
                <x-form.input
                    name="min_level"
                    label="Min Level"
                    tooltip="Minimum level to receive the task"
                    type="number"
                    min="0"
                    :value="$task->min_level"
                />
                <x-form.input
                    name="max_level"
                    label="Max Level"
                    tooltip="Maximum level to receive the task"
                    type="number"
                    min="0"
                    :value="$task->max_level"
                />
                <x-form.input
                    name="level_spread"
                    label="Level Spread"
                    tooltip=""
                    type="number"
                    min="0"
                    :value="$task->level_spread"
                />
                <x-form.input
                    name="min_players"
                    label="Min Players"
                    tooltip=""
                    type="number"
                    min="0"
                    :value="$task->min_players"
                />
                <x-form.input
                    name="max_players"
                    label="Max Players"
                    tooltip=""
                    type="number"
                    min="0"
                    :value="$task->max_players"
                />
                <x-form.input
                    name="dz_template_id"
                    label="DZ Template ID"
                    tooltip=""
                    type="number"
                    min="0"
                    :value="$task->dz_template_id"
                />
                <div class="form-control w-full col-span-2">
                    <label for="lock_activity_id" class="label">
                        <span class="label-text">Lock Task on Activity ID</span>
                    </label>
                    <select
                        name="lock_activity_id"
                        id="lock_activity_id"
                        x-model="lockActivityId"
                        x-data="{ lockActivityId: {{ $task->lock_activity_id ?? 0 }} }"
                        class="select w-full"
                    >
                        <option value="-1">None</option>
                        <template x-for="opt in $store.taskActivities.selectOptions()" :key="opt.value">
                            <option :value="opt.value" :selected="opt.value == lockActivityId" x-text="opt.label"></option>
                        </template>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="card bg-base-200 shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Timers</h2>
            <div class="grid grid-cols-4 gap-4">
                <div x-data="{ }" class="w-full">
                    <label class="label">Replay Timer Group</label>
                    <div class="flex join">
                        <input
                            name="replay_timer_group"
                            type="number"
                            min="0"
                            value="{{ $task->replay_timer_group }}"
                            class="input w-full join-item"
                        />
                        <button type="button" class="btn btn-soft btn-accent join-item"
                            @click="$dispatch('open-timer-groups', {
                                field: 'replay',
                                inputName: 'replay_timer_group' })">
                            Pick
                        </button>
                    </div>
                </div>
                <x-form.input
                    name="replay_timer_seconds"
                    label="Replay Timer (s)"
                    type="number"
                    min="0"
                    :value="$task->replay_timer_seconds"
                />
                <div x-data="{ }" class="w-full">
                    <label class="label">Request Timer Group</label>
                    <div class="flex join">
                        <input
                            name="request_timer_group"
                            type="number"
                            min="0"
                            value="{{ $task->request_timer_group }}"
                            class="input w-full join-item"
                        />
                        <button type="button" class="btn btn-soft btn-accent join-item"
                            @click="$dispatch('open-timer-groups', {
                                field: 'request',
                                inputName: 'request_timer_group'
                            })">
                            Pick
                        </button>
                    </div>
                </div>
                <x-form.input
                    name="request_timer_seconds"
                    label="Request Timer (s)"
                    type="number"
                    min="0"
                    :value="$task->request_timer_seconds"
                    x-model="task.request_timer_seconds"
                />
            </div>
        </div>
    </div>
    <div class="card bg-base-200 shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Completion</h2>
            <div class="grid grid-cols-6 gap-4">
                <x-form.select
                    name="reward_method"
                    label="Reward Method"
                    :options="[
                        0 => 'Single Item ID',
                        1 => 'List of Items',
                        2 => 'Quest Controlled',
                    ]"
                    :selected="$task->reward_method"
                />
                <x-form.input
                    name="reward_text"
                    label="Reward Text"
                    tooltip=""
                    maxlength="64"
                    :value="$task->reward_text"
                    wrapper-class="col-span-2"
                />
                <div
                    x-data='ajaxSelect({
                        searchUrl: "/items/search",
                        useModal: false,
                        prefillValue: @json($task->rewards),
                        allowNone: true,
                        noneId: 0,
                        multiple: true,
                    })'
                    x-init="init()"
                    class="col-span-3"
                >
                    <label class="label">Reward Items List</label>
                    <select
                        x-ref="select"
                        name="reward_id_list[]"
                        multiple
                        class="w-full validator invalid:select-error"
                        tooltip="Item ID(s) for the rewarded item(s) separated by | Ex: (1001|1002|1003)"
                    ></select>
                </div>
                <x-form.select-alt-currency
                    name="reward_point_type"
                    label="Reward Point Type"
                    :options="$altCurrency"
                    :selected="$task->reward_point_type"
                    placeholder="Reward Point Type"
                />
                <x-form.input
                    name="reward_points"
                    label="Reward Points"
                    tooltip=""
                    type="number"
                    min="0"
                    :value="$task->reward_points"
                />
                <x-form.input
                    name="exp_reward"
                    label="EXP Reward"
                    tooltip=""
                    type="number"
                    min="0"
                    :value="$task->exp_reward"
                />
                <div x-data="currencyHelper({{ $task->cash_reward ?? 0 }})">
                    <x-form.input
                        name="cash_reward"
                        label="Cash Reward"
                        tooltip="Amount of coin rewarded in copper"
                        :value="$task->cash_reward"
                        x-model.number="amount"
                        x-bind:label-suffix="true"
                    />
                </div>
                <div
                    x-data='ajaxSelect({
                        searchUrl: "/factions/search",
                        useModal: false,
                        prefillValue: @json($task->faction),
                        allowNone: true,
                        noneId: 0,
                        keyInOption: true,
                    })'
                    x-init="init()"
                >
                    <label class="label">Faction Reward ID</label>
                    <select
                        x-ref="select"
                        tooltip="Faction ID for reward"
                        name="faction_reward"
                        class="w-full validator invalid:select-error"
                    ></select>
                </div>
                <x-form.input
                    name="faction_amount"
                    label="Faction Amount"
                    tooltip=""
                    type="number"
                    min="0"
                    :value="$task->faction_amount"
                />
                <x-form.textarea
                    name="completion_emote"
                    label="Completion Emote"
                    :value="$task->completion_emote"
                    wrapper-class="col-span-6"
                />
            </div>
        </div>
    </div>
    <div class="card bg-neutral shadow-sm">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <x-form.checkbox
                        name="enabled"
                        label="Enabled"
                        :checked="$task->enabled"
                    />
                    <x-form.checkbox
                        name="repeatable"
                        label="Repeatable"
                        :checked="$task->repeatable"
                    />
                </div>
                <div>
                    <button type="submit" class="btn btn-soft btn-success">Save Task</button>
                </div>
            </div>
        </div>
    </div>
    @include('tasks.partials.modal-timer-group')
</div>
