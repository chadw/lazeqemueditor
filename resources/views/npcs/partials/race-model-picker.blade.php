<div x-cloak x-show="$store.raceModelPicker.isOpen" class="fixed inset-0 z-50">
    <div class="fixed inset-0 bg-black/50" @click="$store.raceModelPicker.close()"></div>
    <div id="race-model-modal" class="relative mx-auto my-12 max-w-7xl bg-base-100 rounded shadow-lg overflow-hidden">
        <div class="flex items-center justify-between p-4 border-b border-base-content/10">
            <h3 class="text-lg font-semibold">Races</h3>
            <div class="flex items-center gap-2">
                <button
                    type="button"
                    class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4"
                    @click="$store.raceModelPicker.close()"
                >
                    ✕
                </button>
            </div>
        </div>

        <div class="p-4">
            <div class="mb-4 text-sm text-base-content/70">Click a model to select it — this will populate `race`, `gender`, `texture`, and `helmtexture` fields.</div>

            <div class="flex flex-wrap gap-4 max-h-[75vh] overflow-auto px-1">
                <template x-for="group in $store.raceModelPicker.grouped" :key="group.raceId">
                    <div x-bind:data-race-id="group.raceId" class="p-3 rounded bg-base-200 inline-grid grid-flow-row border border-base-content/10"
                         :class="{ 'ring-2 ring-primary': group.raceId === $store.raceModelPicker.selectedRaceId }">
                        <div class="flex justify-between mb-2">
                            <div class="font-medium" x-text="group.label"></div>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <template x-for="m in group.items" :key="m.className">
                                <button type="button"
                                    class="race-model relative cursor-pointer rounded-lg border border-base-300 bg-base-200 hover:border-primary hover:shadow-lg transition-all duration-150"
                                    :class="{
                                        [m.className]: true,
                                        'ring-1 ring-base-200': true,
                                        'border-2 border-secondary shadow-[0_0_12px_var(--color-secondary)]': $store.raceModelPicker.selectedClassName === m.className
                                    }"
                                    :title="`Race: ${m.race} • Gender: ${m.gender} • Texture: ${m.texture} • Helm: ${m.helm}`"
                                    @click="$store.raceModelPicker.select(m)">
                                    <span class="sr-only" x-text="m.className"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script type="text/javascript">
    window.raceNames = @json(config('everquest.db_races'));
</script>
@endpush
