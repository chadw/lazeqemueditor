{{-- slots --}}
<div class="card bg-base-200 card-sm shadow-sm">
    <div class="card-body">
        <div x-data="bitMaskPicker({ initial: {{ $item->slots ?? 0 }}, fieldName: 'slots' })">
            <h2 class="card-title mb-4">Slots
                <span class="text-xs text-neutral-500 tracking-normal" x-text="value"></span>
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2">
                <input type="hidden" :name="fieldName" :value="value">
                @foreach(config('everquest.slots', []) as $k => $label)
                    <div class="form-control">
                        <label class="label cursor-pointer gap-2">
                            <input
                                type="checkbox"
                                value="{{ $k }}"
                                id="slots_{{ $k }}"
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
        <div x-data="bitMaskPicker({ initial: {{ $item->races ?? 0 }}, fieldName: 'races' })">
            <h2 class="card-title mb-4">Races
                <span class="text-xs text-neutral-500 tracking-normal" x-text="value"></span>
            </h2>
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
        <div x-data="bitMaskPicker({ initial: {{ $item->classes ?? 0 }}, fieldName: 'classes' })">
            <h2 class="card-title mb-4">Classes
                <span class="text-xs text-neutral-500 tracking-normal" x-text="value"></span>
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2">
                <input type="hidden" :name="fieldName" :value="value">
                @foreach(config('everquest.classes_bit', []) as $k => $label)
                    <div class="form-control">
                        <label class="label cursor-pointer gap-2">
                            <input
                                type="checkbox"
                                value="{{ $k }}"
                                id="classes_{{ $k }}"
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
        <div x-data="bitMaskPicker({ initial: {{ $item->deity ?? 0 }}, fieldName: 'deity' })">
            <h2 class="card-title mb-4">Deities
                <span class="text-xs text-neutral-500 tracking-normal" x-text="value"></span>
            </h2>
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
