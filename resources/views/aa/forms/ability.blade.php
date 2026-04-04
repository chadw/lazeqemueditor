<div class="card bg-base-200 card-sm shadow-sm mb-4">
    <div class="card-body">
        <div class="grid grid-cols-3 gap-4">
            <x-form.input
                name="name"
                label="Name"
                :value="$ability->name ?? ''"
                required
            />
            <x-form.select
                name="category"
                label="Category"
                :options="config('everquest.aa_categories')"
                :selected="$ability->category ?? -1"
            />
            <x-form.select
                name="type"
                label="Type"
                :options="config('everquest.aa_types')"
                :selected="$ability->type ?? 0"
            />
        </div>
    </div>
</div>

<div class="grid grid-cols-3 gap-4 mb-4">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Classes</h2>
            <div x-data="bitMaskPicker({ initial: {{ $ability->classes ?? 0 }}, fieldName: 'classes' })">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2">
                    <input type="hidden" :name="fieldName" :value="value">
                    @foreach(config('everquest.classes_bit', []) as $k => $label)
                        <div class="form-control">
                            <label class="label cursor-pointer gap-2">
                                <input
                                    type="checkbox"
                                    value="{{ $k }}"
                                    id="classes{{ $k }}"
                                    x-model.number="checked"
                                    class=""
                                />
                                <span class="label-text">{{ $label }}</span>
                            </label>
                        </div>
                    @endforeach
                    <div class="form-control">
                        <label class="label cursor-pointer gap-2">
                            <input
                                type="checkbox"
                                data-all
                                x-model="allChecked"
                                @change="toggleAll()"
                                class=""
                            />
                            <span class="label-text font-semibold text-accent">All / None</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Races</h2>
            <div x-data="bitMaskPicker({ initial: {{ $ability->races ?? 0 }}, fieldName: 'races' })">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2">
                    <input type="hidden" :name="fieldName" :value="value">
                    @foreach(config('everquest.races_bit', []) as $k => $label)
                        <div class="form-control">
                            <label class="label cursor-pointer gap-2">
                                <input
                                    type="checkbox"
                                    value="{{ $k }}"
                                    id="races_{{ $k }}"
                                    x-model.number="checked"
                                    class=""
                                />
                                <span class="label-text">{{ $label }}</span>
                            </label>
                        </div>
                    @endforeach
                    <div class="form-control">
                        <label class="label cursor-pointer gap-2">
                            <input
                                type="checkbox"
                                data-all
                                x-model="allChecked"
                                @change="toggleAll()"
                                class=""
                            />
                            <span class="label-text font-semibold text-accent">All / None</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Deities</h2>
            <div x-data="bitMaskPicker({ initial: {{ $ability->deities ?? 0 }}, fieldName: 'deities' })">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2">
                    <input type="hidden" :name="fieldName" :value="value">
                    @foreach(config('everquest.deities_bit', []) as $k => $label)
                        <div class="form-control">
                            <label class="label cursor-pointer gap-2">
                                <input
                                    type="checkbox"
                                    value="{{ $k }}"
                                    id="deity_{{ $k }}"
                                    x-model.number="checked"
                                    class=""
                                />
                                <span class="label-text">{{ $label }}</span>
                            </label>
                        </div>
                    @endforeach
                    <div class="form-control">
                        <label class="label cursor-pointer gap-2">
                            <input
                                type="checkbox"
                                data-all
                                x-model="allChecked"
                                @change="toggleAll()"
                                class=""
                            />
                            <span class="label-text font-semibold text-accent">All / None</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card bg-base-200 card-sm shadow-sm mb-4">
    <div class="card-body">
        <div class="grid grid-cols-4 gap-4 mb-4">
            <x-form.input
                name="first_rank_id"
                label="First Rank"
                :value="$ability->first_rank_id ?? 0"
            />
            <x-form.input
                name="status"
                label="Status"
                :value="$ability->status ?? 0"
            />
            <x-form.input
                name="charges"
                label="Charges"
                :value="$ability->charges ?? 0"
            />
            <x-form.input
                name="drakkin_heritage"
                label="Drakkin Heritage"
                :value="$ability->drakkin_heritage ?? 127"
            />
        </div>
        <div class="flex flex-wrap items-center gap-4">
            <x-form.checkbox
                name="enabled"
                label="Enabled"
                :checked="$ability->enabled ?? true"
            />
            <x-form.checkbox
                name="grant_only"
                label="Grant Only"
                :checked="$ability->grant_only ?? false"
            />
            <x-form.checkbox
                name="auto_grant_enabled"
                label="Auto Grant"
                :checked="$ability->auto_grant_enabled ?? false"
            />
            <x-form.checkbox
                name="reset_on_death"
                label="Reset on Death"
                :checked="$ability->reset_on_death ?? false"
            />
        </div>
    </div>
</div>
