<div>
    @php $slotsInv = config('everquest.slots_inv', []); @endphp

    {{-- Gear / worn --}}
    <div class="collapse collapse-arrow bg-base-300 rounded-box border border-base-content/10">
        <input type="checkbox" id="inv-gear-{{ $character->id ?? '0' }}" class="hidden" />
        <label for="inv-gear-{{ $character->id ?? '0' }}" class="collapse-title flex items-center justify-between">
            <div class="text-sm text-muted">Gear</div>
            <div class="text-sm">{{ $items['gear']->count() }}</div>
        </label>
        <div class="collapse-content p-0">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-3 p-6 pt-0 mt-3">
                @forelse($items['gear'] as $inv)
                    @php
                        $it = $inv->item ?? null;
                        $slotKey = $inv->slot_id ?? ($inv->equip_slot ?? null);
                        $slotLabel = $slotsInv[$slotKey] ?? ($slotKey !== null ? "Slot {$slotKey}" : 'Unknown');
                    @endphp
                    <div class="relative bg-base-200 rounded shadow-sm flex flex-col items-start p-2">
                        @if(optional($inv)->charges)
                            <span class="badge badge-xs badge-soft badge-accent absolute -top-1 -right-1">{{ $inv->charges }}</span>
                        @endif
                        <div class="w-full flex items-center truncate">
                            @if ($it)
                                <x-item-link
                                    :item_id="$it->id"
                                    :item_name="$it->Name"
                                    :item_icon="$it->icon"
                                    item_class=""
                                />
                            @else
                                <span class="text-xs text-muted">—</span>
                            @endif
                        </div>
                        <div class="w-full text-xs text-muted pl-11">
                            ID: {{ $it->id ?? ($inv->item_id ?? '-') }} ·
                            {{ $slotLabel }} · x{{ $inv->charges ?? 1 }}
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-muted">No equipped gear.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Bags --}}
    <div class="collapse collapse-arrow bg-base-300 rounded-box border border-base-content/10 mt-3">
        <input type="checkbox" id="inv-bags-{{ $character->id ?? '0' }}" class="hidden" />
        <label for="inv-bags-{{ $character->id ?? '0' }}" class="collapse-title flex items-center justify-between">
            <div class="text-sm text-muted">Bags</div>
            <div class="text-sm">{{ $items['bags']->count() }}</div>
        </label>
        <div class="collapse-content p-0">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-3 p-6 pt-0 mt-3">
                @forelse($items['bags'] as $bag)
                    @php
                        $bitem = $bag->item ?? null;
                        $slotKey = $bag->slot_id ?? $bag->equip_slot ?? null;
                        $slotLabel = $slotsInv[$slotKey] ?? ($slotKey !== null ? "Slot {$slotKey}" : 'Bag');
                    @endphp
                    <div class="relative bg-base-200 rounded shadow-sm flex flex-col items-start p-2">
                        <div class="w-full flex items-center truncate">
                            @if ($bitem)
                                <x-item-link
                                    :item_id="$bitem->id"
                                    :item_name="$bitem->Name"
                                    :item_icon="$bitem->icon"
                                    item_class=""
                                />
                            @else
                                <span class="text-xs text-muted">Bag</span>
                            @endif
                        </div>
                        <div class="w-full text-xs text-muted pl-11">
                            ID: {{ $it->bitem ?? ($bag->item_id ?? '-') }} ·
                            {{ $slotLabel }}
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-muted">No bags.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Items inside bags --}}
    <div class="collapse collapse-arrow bg-base-300 rounded-box border border-base-content/10 mt-3">
        <input type="checkbox" id="inv-bag-items-{{ $character->id ?? '0' }}" class="hidden" />
        <label for="inv-bag-items-{{ $character->id ?? '0' }}" class="collapse-title flex items-center justify-between">
            <div class="text-sm text-muted">Bag Contents</div>
            <div class="text-sm">{{ $items['bag_items']->count() }}</div>
        </label>
        <div class="collapse-content p-0">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-3 p-6 pt-0 mt-3">
                @forelse($items['bag_items'] as $bi)
                    @php $it = $bi->item ?? null; @endphp
                    <div class="relative bg-base-200 rounded shadow-sm flex flex-col items-start p-2">
                        @if(optional($bi)->charges)
                            <span class="badge badge-xs badge-soft badge-accent absolute -top-1 -right-1">{{ $bi->charges }}</span>
                        @endif
                        <div class="w-full flex items-center truncate">
                            @if ($it)
                                <x-item-link
                                    :item_id="$it->id"
                                    :item_name="$it->Name"
                                    :item_icon="$it->icon"
                                    item_class=""
                                />
                            @else
                                <span class="text-xs text-muted">—</span>
                            @endif
                        </div>
                        <div class="w-full text-xs text-muted pl-11">
                            ID: {{ $it->id ?? ($bi->item_id ?? '-') }} ·
                            Slot {{ $bi->slot_id }}
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-muted">No items in bags.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Bank --}}
    <div class="collapse collapse-arrow bg-base-300 rounded-box border border-base-content/10 mt-3">
        <input type="checkbox" id="inv-bank-{{ $character->id ?? '0' }}" class="hidden" />
        <label for="inv-bank-{{ $character->id ?? '0' }}" class="collapse-title flex items-center justify-between">
            <div class="text-sm text-muted">Bank</div>
            <div class="text-sm">{{ $items['bank']->count() }}</div>
        </label>
        <div class="collapse-content p-0">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-3 p-6 pt-0 mt-3">
                @forelse($items['bank'] as $b)
                    @php $it = $b->item ?? null; @endphp
                    <div class="relative bg-base-200 rounded shadow-sm flex flex-col items-start p-2">
                        <div class="w-full flex items-center truncate">
                            @if ($it)
                                <x-item-link
                                    :item_id="$it->id"
                                    :item_name="$it->Name"
                                    :item_icon="$it->icon"
                                    item_class=""
                                />
                            @else
                                <span class="text-xs text-muted">—</span>
                            @endif
                        </div>
                        <div class="w-full text-xs text-muted pl-11">
                            ID: {{ $it->bitem ?? ($b->item_id ?? '-') }} ·
                            Slot {{ $b->slot_id }}
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-muted">No bank items.</div>
                @endforelse
            </div>
        </div>
    </div>


    {{-- Items inside bank --}}
    <div class="collapse collapse-arrow bg-base-300 rounded-box border border-base-content/10 mt-3">
        <input type="checkbox" id="inv-bank-items-{{ $character->id ?? '0' }}" class="hidden" />
        <label for="inv-bank-items-{{ $character->id ?? '0' }}" class="collapse-title flex items-center justify-between">
            <div class="text-sm text-muted">Bank Contents</div>
            <div class="text-sm">{{ $items['bank_items']->count() }}</div>
        </label>
        <div class="collapse-content p-0">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-3 p-6 pt-0 mt-3">
                @forelse($items['bank_items'] as $bi)
                    @php $it = $bi->item ?? null; @endphp
                    <div class="relative bg-base-200 rounded shadow-sm flex flex-col items-start p-2">
                        @if(optional($bi)->charges)
                            <span class="badge badge-xs badge-soft badge-accent absolute -top-1 -right-1">{{ $bi->charges }}</span>
                        @endif
                        <div class="w-full flex items-center truncate">
                            @if ($it)
                                <x-item-link
                                    :item_id="$it->id"
                                    :item_name="$it->Name"
                                    :item_icon="$it->icon"
                                    item_class=""
                                />
                            @else
                                <span class="text-xs text-muted">—</span>
                            @endif
                        </div>
                        <div class="w-full text-xs text-muted pl-11">
                            ID: {{ $it->id ?? ($bi->item_id ?? '-') }} ·
                            Slot {{ $bi->slot_id }}
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-muted">No items in bags.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Shared bank --}}
    <div class="collapse collapse-arrow bg-base-300 rounded-box border border-base-content/10 mt-3">
        <input type="checkbox" id="inv-shared-bank-{{ $character->id ?? '0' }}" class="hidden" />
        <label for="inv-shared-bank-{{ $character->id ?? '0' }}" class="collapse-title flex items-center justify-between">
            <div class="text-sm text-muted">Shared Bank</div>
            <div class="text-sm">{{ $items['shared_bank']->count() }}</div>
        </label>
        <div class="collapse-content p-0">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-3 p-6 pt-0 mt-3">
                @forelse($items['shared_bank'] as $sb)
                    @php $it = $sb->item ?? null; @endphp
                    <div class="relative bg-base-200 rounded shadow-sm flex flex-col items-start p-2">
                        <div class="w-full flex items-center truncate">
                            @if ($it)
                                <x-item-link
                                    :item_id="$it->id"
                                    :item_name="$it->Name"
                                    :item_icon="$it->icon"
                                    item_class=""
                                />
                            @else
                                <span class="text-xs text-muted">—</span>
                            @endif
                        </div>
                        <div class="w-full text-xs text-muted pl-11">
                            ID: {{ $it->bitem ?? ($sb->item_id ?? '-') }} ·
                            Slot {{ $sb->slot_id }}
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-muted">No shared bank items.</div>
                @endforelse
            </div>
        </div>
    </div>

</div>
