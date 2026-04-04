<div class="space-y-6">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div x-data="bitMaskPicker({ initial: {{ $item->augtype ?? 0 }}, fieldName: 'augtype' })">
                <h2 class="card-title mb-4">Type
                    <span class="text-xs text-neutral-500 tracking-normal" x-text="value"></span>
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    <input type="hidden" :name="fieldName" :value="value">
                    @foreach(config('everquest.aug_type_descriptions', []) as $k => $label)
                        <div class="form-control">
                            @php
                                $bit = 1 << ((int)$k - 1);
                            @endphp
                            <label class="label cursor-pointer gap-2">
                                <input
                                    type="checkbox"
                                    value="{{ $bit }}"
                                    id="augtype_{{ $k }}"
                                    x-model.number="checked"
                                    class="checkbox checked:checkbox-success"
                                />
                                <span class="label-text">Type {{ $k }}: {{ $label }}</span>
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
                                class="checkbox checked:checkbox-success"
                            />
                            <span class="label-text font-semibold text-accent">All / None</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-4">
                <x-form.select
                    name="augrestrict"
                    label="Restriction"
                    tooltip="This restricts the augment to only be inserted in these types of items"
                    :options="[0 => 'None'] + config('everquest.db_aug_restrict')"
                    :selected="$item->augrestrict"
                />
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Item Can Have Augments</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-4">
                <div
                    x-data='ajaxSelect({
                        searchUrl: "/items/search",
                        useModal: false,
                        prefillValue: @json([
                            "id" => $item->augDistillerItem?->id,
                            "name" => ($item->augDistillerItem?->Name ?? null)
                        ]),
                        allowNone: true,
                        noneId: 0,
                        keyInOption: true,
                    })'
                    x-init="init()"
                    class="col-span-2"
                >
                    <label class="label">Distiller Item</label>
                    <select
                        x-ref="select"
                        name="augdistiller"
                        class="w-full validator invalid:select-error"
                    ></select>
                </div>
                <x-form.select
                    name="augslot1type"
                    label="Slot 1 Type"
                    tooltip=""
                    :options="[0 => 'None'] + config('everquest.aug_type_descriptions')"
                    :selected="$item->augslot1type"
                    keyInOption="true"
                />
                <x-form.select
                    name="augslot2type"
                    label="Slot 2 Type"
                    tooltip=""
                    :options="[0 => 'None'] + config('everquest.aug_type_descriptions')"
                    :selected="$item->augslot2type"
                    keyInOption="true"
                />
                <x-form.select
                    name="augslot3type"
                    label="Slot 3 Type"
                    tooltip=""
                    :options="[0 => 'None'] + config('everquest.aug_type_descriptions')"
                    :selected="$item->augslot3type"
                    keyInOption="true"
                />
                <x-form.select
                    name="augslot4type"
                    label="Slot 4 Type"
                    tooltip=""
                    :options="[0 => 'None'] + config('everquest.aug_type_descriptions')"
                    :selected="$item->augslot4type"
                    keyInOption="true"
                />
                <x-form.select
                    name="augslot5type"
                    label="Slot 5 Type"
                    tooltip=""
                    :options="[0 => 'None'] + config('everquest.aug_type_descriptions')"
                    :selected="$item->augslot5type"
                    keyInOption="true"
                />
            </div>
        </div>
    </div>
</div>
