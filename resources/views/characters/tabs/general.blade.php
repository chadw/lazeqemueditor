<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="col-span-1">
        <div class="bg-base-200 p-4 rounded">
            <div class="space-y-2 text-sm">
                <div class="flex items-center gap-3">
                    <div class="text-sm">ID</div>
                    <div class="flex-1 border-b border-dotted border-base-content/20"></div>
                    <div class="font-medium">{{ $character->id }}</div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="text-sm">Name</div>
                    <div class="flex-1 border-b border-dotted border-base-content/20"></div>
                    <div class="font-medium">{{ $character->name }}</div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="text-sm">Last Name</div>
                    <div class="flex-1 border-b border-dotted border-base-content/20"></div>
                    <div class="font-medium">{{ $character->last_name ?: '-' }}</div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="text-sm">Title</div>
                    <div class="flex-1 border-b border-dotted border-base-content/20"></div>
                    <div class="font-medium">{{ $character->title ?: '-' }}</div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="text-sm">Suffix</div>
                    <div class="flex-1 border-b border-dotted border-base-content/20"></div>
                    <div class="font-medium">{{ $character->suffix ?: '-' }}</div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="text-sm">Account</div>
                    <div class="flex-1 border-b border-dotted border-base-content/20"></div>
                    <div class="font-medium">
                        @if ($character->account)
                            <a class="link-accent link-hover"
                                href="{{ route('accounts.show', $character->account->id) }}">
                                {{ $character->account->name }}
                            </a>
                        @else
                            -
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="text-sm">Created</div>
                    <div class="flex-1 border-b border-dotted border-base-content/20"></div>
                    <div class="font-medium">
                        {{ $character->birthday ? \Carbon\Carbon::createFromTimestamp($character->birthday)->format('Y-m-d') : '-' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-base-200 p-4 rounded mt-4">
            <div class="flex items-center justify-between">
                <div class="text-lg font-medium">Location</div>
                <div>
                    <button type="button" class="btn btn-xs btn-soft"
                        @click="(() => {
                            $store.modalForm.openEdit({},
                                '{{ route('characters.move', $character->id) }}',
                                {
                                    modal: 'character-move',
                                    resourceName: 'Move Character',
                                    meta: {
                                        refreshOnSuccess: true,
                                    },
                                    width: 'max-w-3xl'
                                }
                            );
                            $store.modalForm.setField('zone_id', {{ json_encode($character->zone_id) }});
                        })()">
                        <x-ui.icon name="move" /> Move
                    </button>
                </div>
            </div>
            <div class="grid grid-cols-1 gap-3 mt-4">
                <div class="p-3 bg-base-100 rounded flex items-center">
                    <div class="flex-1 text-sm text-muted">Zone</div>
                    <div class="font-medium">{{ optional($character->zone)->short_name ?? $character->zone_id }}</div>
                </div>
            </div>
        </div>

        <div class="bg-base-200 p-4 rounded mt-4">
            <div class="text-lg font-medium my-4 mt-0">Currencies</div>
            <div class="grid grid-cols-1 gap-3 mt-2">
                <div class="p-3 bg-base-100 rounded flex flex-col">
                    <div class="text-sm text-muted">On Player</div>
                    <div class="mt-2">
                        <x-currency
                            :platinum="optional($character->currency)->platinum ?? 0"
                            :gold="optional($character->currency)->gold ?? 0"
                            :silver="optional($character->currency)->silver ?? 0"
                            :copper="optional($character->currency)->copper ?? 0"
                        />
                    </div>
                </div>

                <div class="p-3 bg-base-100 rounded flex flex-col">
                    <div class="text-sm text-muted">Bank</div>
                    <div class="mt-2">
                        <x-currency
                            :platinum="optional($character->currency)->platinum_bank ?? 0"
                            :gold="optional($character->currency)->gold_bank ?? 0"
                            :silver="optional($character->currency)->silver_bank ?? 0"
                            :copper="optional($character->currency)->copper_bank ?? 0"
                        />
                    </div>
                </div>

                <div class="p-3 bg-base-100 rounded flex flex-col">
                    <div class="text-sm text-muted">Shared</div>
                    <div class="mt-2">
                        <x-currency
                            :platinum="$character->account->sharedplat ?? 0"
                        />
                    </div>
                </div>
            </div>

            @if ($character->altCurrency && $character->altCurrency->count())
                <div class="grid grid-cols-1 gap-3">
                    <div class="flex items-center justify-between">
                        <div class="text-lg font-medium my-4">Alternate Currency</div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('alt-currency.characters.index', ['character' => $character->id]) }}"
                                class="btn btn-xs btn-soft">
                                <x-ui.icon name="edit" />
                            </a>
                        </div>
                    </div>
                    @foreach ($character->altCurrency as $alt)
                        <div class="flex items-center gap-3">
                            <div class="text-sm text-muted">
                                @if (optional($alt->altCurrency)->item)
                                    <x-item-link
                                        :item_id="optional($alt->altCurrency->item)->id"
                                        :item_name="optional($alt->altCurrency->item)->Name"
                                        :item_icon="optional($alt->altCurrency->item)->icon"
                                        item_class="flex items-center"
                                    />
                                @else
                                    <span class="text-sm">{{ $alt->alternate_currency_id }}</span>
                                @endif
                            </div>
                            <div class="flex-1 border-b border-dotted border-base-content/20"></div>
                            <div class="font-medium">{{ $alt->amount ?? 0 }}</div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="bg-base-200 p-4 rounded mt-4">
            <div class="text-lg font-medium mb-4">Keys
                <span class="badge badge-sm badge-soft badge-accent ml-2">{{ $character->keys->count() }}</span>
            </div>
            @foreach ($character->keys as $key)
                <div class="group p-2 bg-base-300 border border-base-content/20 relative">
                    <x-item-link
                        :item_id="$key->item->id"
                        :item_name="$key->item->Name"
                        :item_icon="$key->item->icon"
                        item_class="flex"
                    />
                </div>
            @endforeach
        </div>
    </div>

    <div class="col-span-2">
        @if ($character->memmedSpells->isNotEmpty())
            @include('characters.partials.general-memmed-spells')
        @endif

        @include('characters.partials.general-stats')

        @if ($character->bindpoint)
            @include('characters.partials.general-bindpoints')
        @endif

        @if ($character->bandolier)
            @include('characters.partials.general-bandolier')
        @endif

        @if (!empty($character->tribute) && $character->tribute->isNotEmpty())
            @include('characters.partials.general-tribute')
        @endif
    </div>
</div>
