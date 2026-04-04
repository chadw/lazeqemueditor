<div class="space-y-6" x-data x-init="$nextTick(()=>window.evolvingValidate(document.getElementById('evoid')?.value, document.getElementById('evolvinglevel')?.value, document.getElementById('evomax')?.value, {{ $item->id }}))">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-4 gap-4">
                <div class="mt-4">
                <x-form.checkbox
                    name="evoitem"
                    label="Is Evolving"
                    :checked="$item->evoitem"
                />
                </div>
                <div class="form-control w-full">
                    <label for="evoid" class="label">
                        <span class="label-text">Evolution ID</span>
                    </label>
                    <div class="flex gap-2">
                        <input id="evoid" name="evoid" type="number" autocomplete="off"
                            value="{{ old('evoid', $item->evoid) }}"
                            class="input w-full text-right"
                            @input.debounce.300ms="window.evolvingValidate($event.target.value, document.getElementById('evolvinglevel')?.value, document.getElementById('evomax')?.value, {{ $item->id }})" />
                        <button type="button" class="btn btn-soft btn-secondary"
                            @click="$store.evolvingPicker.open('evoid')" title="Pick Evolving ID">
                            <x-ui.icon name="search" />
                        </button>
                    </div>
                </div>
                <x-form.input
                    id="evolvinglevel"
                    name="evolvinglevel"
                    label="Level"
                    type="number"
                    :value="$item->evolvinglevel"
                    x-on:input.debounce.300ms="window.evolvingValidate(document.getElementById('evoid')?.value, $event.target.value, document.getElementById('evomax')?.value, {{ $item->id }})"
                />
                <x-form.input
                    id="evomax"
                    name="evomax"
                    label="Max Level"
                    type="number"
                    :value="$item->evomax"
                    x-on:input.debounce.300ms="window.evolvingValidate(document.getElementById('evoid')?.value, document.getElementById('evolvinglevel')?.value, $event.target.value, {{ $item->id }})"
                />
            </div>
        </div>
    </div>

    <template x-if="$store.evolvingPicker && $store.evolvingPicker.validation && $store.evolvingPicker.validation.messages && $store.evolvingPicker.validation.messages.length">
            <div role="alert" class="alert alert-warning alert-soft flex flex-col items-start gap-2">
            <div class="font-semibold">Evolving configuration warnings</div>
            <ul class="list-disc list-inside text-sm mt-1">
                <template x-for="msg in $store.evolvingPicker.validation.messages" :key="msg">
                    <li x-text="msg"></li>
                </template>
            </ul>
        </div>
    </template>

    @if ($item->evolvingDetails->isNotEmpty())
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Evolving Item Details</h2>
        </div>

        <div class="border border-base-content/5 overflow-x-auto">
            <table class="table table-auto table-zebra md:table-fixed w-full">
                <thead class="text-xs uppercase bg-neutral">
                    <tr>
                        <th scope="col" class="w-[5%]">Evo ID</th>
                        <th scope="col" class="w-[5%]">Level</th>
                        <th scope="col">Item</th>
                        <th scope="col" class="w-[10%]">Type</th>
                        <th scope="col">SubType</th>
                        <th scope="col" class="w-[10%] text-right">Req Amount</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($item->evolvingDetails as $detail)
                    @php
                        $subTypes = explode('.', $detail->sub_type);
                        $sourceArray = match((int)$detail->type) {
                            1 => config('everquest.evolving_item_subtypes'),
                            3 => config('everquest.db_races'),
                            4 => $zones,
                            default => []
                        };
                    @endphp
                    <tr class="{{ $detail->item_id == $item->id ? 'bg-info/10 font-bold' : 'text-base-content/30' }}">
                        <td>{{ $detail->item_evo_id }}</td>
                        <td>{{ $detail->item_evolve_level }}</td>
                        <td>
                            <x-item-link
                                :item_id="$detail->item->id"
                                :item_name="$detail->item->Name"
                                :item_icon="$detail->item->icon"
                                item_class="flex"
                            />
                        </td>
                        <td>
                            {{ config('everquest.evolving_item_types')[$detail->type] ?? 'Unknown' }}
                        </td>
                        <td>
                            @if ($sourceArray)
                                @foreach($subTypes as $subId)
                                    @php
                                        if ($detail->type == 4 && is_object($sourceArray)) {
                                            $label = $sourceArray->firstWhere('zoneidnumber', $subId)->short_name ?? $subId;
                                        } else {
                                            $label = $sourceArray[$subId] ?? $subId;
                                        }
                                    @endphp
                                    <div class="inline-flex items-center rounded-md bg-base-300 overflow-hidden border border-base-content/10 mr-1 mb-1">
                                        <span class="px-2 py-1 bg-neutral text-neutral-content/50 text-xs font-mono font-bold">
                                            {{ $subId }}
                                        </span>
                                        <span class="px-2 py-1 text-xs whitespace-nowrap">
                                            {{ $label }}
                                        </span>
                                    </div>
                                @endforeach
                            @else
                                {{ config('everquest.evolving_item_subtypes.' . $detail->sub_type) }}
                            @endif
                        </td>
                        <td class="text-right">{{ $detail->required_amount }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
