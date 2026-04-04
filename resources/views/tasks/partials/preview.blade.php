<div class="card bg-base-300 shadow-xl sticky top-0.5 border border-base-content/10">
    <div class="card-body p-5 space-y-4">
        <div class="space-y-1">
            <div class="flex justify-between items-start">
                <h2 class="text-xl font-bold text-info leading-tight flex-1 mr-4"
                    x-text="task.title || 'Untitled Task'"></h2>
                <div class="text-right shrink-0">
                    <span class="text-[10px] uppercase block font-bold">
                        Request Timer
                        <span class="ml-1 font-mono text-secondary" x-text="(task.request_timer_seconds || 0) + 's'"></span>
                    </span>
                </div>
            </div>
            <div class="flex justify-between items-center text-xs border-b border-base-content/10 pb-2">
                <span class="opacity-60" x-text="types[task.type] || 'Task'"></span>
                <span class="text-warning font-bold">Time Left: <span
                        x-text="task.duration_code === 0 ? 'Unlimited' : (task.duration || 0) + 's'"></span></span>
            </div>
        </div>

        <div class="overflow-hidden rounded border border-base-content/20">
            <table class="table table-xs w-full bg-base-100/50">
                <thead class="bg-base-200">
                    <tr class="text-[10px] uppercase opacity-70">
                        <th class="py-2">Instructions</th>
                        <th class="text-center">Status</th>
                        <th>Zone</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-base-content/5">
                    <template x-for="act in $store.taskActivities.items" :key="act.activityid">
                        <tr>
                            <td class="py-3 leading-snug whitespace-normal"
                                x-text="$store.taskActivities.describe(act)"></td>
                            <td class="text-center font-mono opacity-80" x-text="'0/' + act.goalcount"></td>
                            <td class="text-xs opacity-60" x-text="act.zone?.long_name ? act.zone.long_name : 'Any'">
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- rewards --}}
        <div class="space-y-2">
            <h3 class="text-[10px] uppercase font-bold opacity-50 tracking-widest">Reward(s)</h3>
            <div class="space-y-1">
                <template x-if="task.reward_id_list">
                    <div class="flex items-center gap-2 group cursor-help">
                        <div
                            class="w-6 h-6 bg-base-100 border border-purple-500/30 rounded flex items-center justify-center">
                            <span class="text-purple-400">🎁</span>
                        </div>
                        <span class="text-purple-400 font-medium hover:underline"
                            x-text="'Item ID: ' + task.reward_id_list"></span>
                    </div>
                </template>
                <div class="text-[11px] text-purple-300/70 italic" x-text="task.reward_text"></div>
            </div>
            <div class="pt-4 flex justify-start items-center gap-3 border-t border-base-content/10">
                <span class="text-[10px] uppercase font-bold opacity-40">Cash</span>
                <div class="flex items-center gap-3" x-data>
                    <div class="flex items-center gap-1">
                        <span class="font-mono font-bold" x-text="Math.floor(((task?.cash_reward)||0) / 1000)"></span>
                        <div class="w-3 h-3 rounded-full bg-slate-300" title="Platinum"></div>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="font-mono font-bold" x-text="Math.floor(((task?.cash_reward)||0) % 1000 / 100)"></span>
                        <div class="w-3 h-3 rounded-full bg-yellow-500" title="Gold"></div>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="font-mono font-bold" x-text="Math.floor(((task?.cash_reward)||0) % 100 / 10)"></span>
                        <div class="w-3 h-3 rounded-full bg-gray-400" title="Silver"></div>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="font-mono font-bold" x-text="((task?.cash_reward)||0) % 10"></span>
                        <div class="w-3 h-3 rounded-full bg-amber-700" title="Copper"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
