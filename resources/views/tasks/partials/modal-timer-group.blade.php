<div x-data x-cloak @open-timer-groups.window="$store.timerGroups.open($event.detail)">
    <div x-show="$store.timerGroups.visible" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-base-100 rounded-lg shadow-xl w-11/12 max-w-3xl p-4" @click.away="$store.timerGroups.close()">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Timer Groups — <span x-text="$store.timerGroups.field"></span></h3>
                <button type="button" class="btn btn-sm" @click="$store.timerGroups.close()">Close</button>
            </div>

            <div class="space-y-4 max-h-200 overflow-auto">
                <template x-for="g in $store.timerGroups.groups" :key="g.id">
                    <div class="border rounded p-3">
                        <div class="flex items-center justify-between">
                            <div class="font-medium">Group <span x-text="g.id"></span></div>
                            <div class="text-sm text-muted">Count: <span x-text="g.tasks.length"></span></div>
                        </div>
                        <ul class="list-disc ml-5 mt-2 text-sm">
                            <template x-for="t in g.tasks" :key="t.id">
                                <li>
                                    <div>
                                        <span class="text-xs text-muted">#<span x-text="t.id"></span> - </span>
                                        <span x-text="t.title"></span>
                                    </div>
                                </li>
                            </template>
                        </ul>
                        <div class="mt-2">
                            <button type="button" class="btn btn-xs" @click="$store.timerGroups.select(g)">Select</button>
                        </div>
                    </div>
                </template>
                <div x-show="!$store.timerGroups.groups.length" class="text-center text-sm text-muted">
                    No timer groups found
                </div>
            </div>
        </div>
    </div>
</div>
