<div class="space-y-6">
    <div class="card bg-base-200 shadow-sm mb-6">
        <div class="card-body">
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <span class="font-semibold">Task:</span>
                    <span x-text="$store.modalForm.form.task.title"></span>
                    (<span x-text="$store.modalForm.form.task_id"></span>)
                </div>
                <template x-if="$store.modalForm.form.dz">
                    <div>
                        <span class="font-semibold">DZ:</span>
                        <span :class="$store.modalForm.form.dz?.dynamic_zone_id ? 'text-success' : 'text-error'"
                            x-text="$store.modalForm.form.dz?.dynamic_zone_id ? 'Yes' : 'No'">
                        </span>
                    </div>
                </template>
                <template x-if="!$store.modalForm.form.dz">
                    <div></div>
                </template>
                <div>
                    <span class="font-semibold">Locked:</span>
                    <span x-text="$store.modalForm.form.is_locked ? 'Yes' : 'No'"
                        :class="$store.modalForm.form.is_locked ? 'text-error' : 'text-success'">
                    </span>
                </div>
                <div>
                    <span class="font-semibold">Accepted At:</span>
                    <span
                        x-text="$store.modalForm.form.accepted_time
                        ? new Date($store.modalForm.form.accepted_time).toLocaleString([], {dateStyle: 'short', timeStyle: 'short'})
                        : 'N/A'">
                    </span>
                </div>
                <div>
                    <span class="font-semibold">Completed At:</span>
                    <span
                        x-text="$store.modalForm.form.completion_time
                        ? new Date($store.modalForm.form.completion_time).toLocaleString([], {dateStyle: 'short', timeStyle: 'short'})
                        : 'N/A'">
                    </span>
                </div>
                <div>
                    <span class="font-semibold">Expires At:</span>
                    <span
                        x-text="$store.modalForm.form.expire_time
                        ? new Date($store.modalForm.form.expire_time).toLocaleString([], {dateStyle: 'short', timeStyle: 'short'})
                        : 'N/A'">
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-200 shadow-sm mb-6">
        <div class="card-body">
            <h2 class="card-title">Members</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
                <template x-for="member in $store.modalForm.form.members" :key="member.character_id">
                    <div
                        class="bg-base-100 p-3 rounded-lg border border-base-300 flex flex-col items-center justify-center text-center shadow-sm hover:border-base-content/20 transition-colors">
                        <span class="font-bold text-sm leading-tight"
                            x-text="member.character?.name || 'Unknown'"></span>
                        <span class="text-[10px] uppercase tracking-wider font-semibold opacity-60 italic"
                            x-text="classes[member.character?.class] || 'Unknown Class'">
                        </span>
                        <span class="text-[10px] opacity-40 mt-1" x-text="'ID: ' + member.character_id"></span>
                        <template x-if="member.is_leader">
                            <div class="badge badge-sm badge-soft badge-accent mt-2 font-bold text-[10px]">LEADER</div>
                        </template>
                        <template x-if="!member.is_leader">
                            <div class="badge badge-sm badge-soft mt-2 opacity-50 text-[10px]">MEMBER</div>
                        </template>
                    </div>
                </template>
                <template x-if="!$store.modalForm.form.members || $store.modalForm.form.members.length === 0">
                    <div class="col-span-full py-4 text-center opacity-50 italic">
                        No members assigned to this task.
                    </div>
                </template>
            </div>
        </div>
    </div>

    <div class="card bg-base-200 shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Task Activities</h2>
            <div class="overflow-x-auto">
                <table class="table bg-base-100 rounded-lg overflow-hidden border border-base-300">
                    <thead class="bg-base-300/50">
                        <tr>
                            <th>ID</th>
                            <th>Description</th>
                            <th>Type</th>
                            <th class="text-center">Progress</th>
                            <th>Last Update</th>
                            <th class="text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="state in $store.modalForm.form.activity_states" :key="state.activity_id">
                            <tr class="hover:bg-base-200/50 transition-colors" x-data="{
                                activityDef: $store.modalForm.form.task_activities.find(a => a.activityid === state.activity_id)
                            }">
                                <td x-text="state.activity_id"></td>
                                <td class="font-medium">
                                    <span
                                        x-text="activityDef
                                        ? $store.taskActivities.describe(activityDef)
                                        : 'No Activity Data'"></span>
                                </td>
                                <td>
                                    <span class="badge badge-ghost badge-sm"
                                        x-text="activityTypes[activityDef?.activitytype] ?? 'N/A'"></span>
                                </td>
                                <td class="text-center">
                                    <div class="flex flex-col items-center justify-center gap-1">
                                        <div class="flex font-bold text-xs">
                                            <span x-text="state.done_count"></span>
                                            <span class="opacity-50"
                                                x-text="'/' + (activityDef?.goalcount || 0)"></span>
                                        </div>
                                        <progress class="progress progress-info w-16 h-1.5" :value="state.done_count"
                                            :max="activityDef?.goalcount || 100">
                                        </progress>
                                    </div>
                                </td>
                                <td class="text-xs opacity-70">
                                    <span
                                        x-text="state.updated_time
                                            ? new Date(state.updated_time).toLocaleString([], {dateStyle: 'short', timeStyle: 'short'})
                                            : 'N/A'"></span>
                                </td>
                                <td class="text-right">
                                    <template x-if="state.completed_time">
                                        <div class="flex flex-row justify-end items-center gap-2">
                                            <span
                                                class="badge badge-sm badge-soft badge-success uppercase text-[10px] font-bold">Done</span>
                                            <span class="opacity-40 text-xs"
                                                x-text="new Date(state.completed_time).toLocaleDateString()"></span>
                                        </div>
                                    </template>
                                    <template x-if="!state.completed_time">
                                        <span
                                            class="badge badge-sm badge-soft badge-warning uppercase text-[10px] font-bold">Active</span>
                                    </template>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            <template
                x-if="!$store.modalForm.form.activity_states || $store.modalForm.form.activity_states.length === 0">
                <div class="text-center py-4 opacity-40 italic text-sm">
                    No activities recorded.
                </div>
            </template>
        </div>
    </div>
</div>
