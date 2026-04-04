<div class="space-y-6">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-2 gap-4">
                <div class="rounded-lg border border-base-300 p-3 bg-base-100">
                    <div class="text-sm font-medium mb-2">Faction Mod 1</div>
                    <div class="grid grid-cols-2 gap-3">
                        <div
                            x-data='ajaxSelect({
                                searchUrl: "/factions/search",
                                useModal: false,
                                prefillValue: @json([
                                    "id" => $item->factionmod1,
                                    "name" => ($factions[$item->factionmod1] ?? null)
                                ]),
                                keyInOption: true,
                            })'
                            x-init="init()"
                        >
                            <label class="label">Faction</label>
                            <select
                                x-ref="select"
                                name="factionmod1"
                                class="w-full validator invalid:select-error"
                            ></select>
                        </div>
                        <x-form.input
                            name="factionamt1"
                            label="Amount"
                            type="number"
                            :value="$item->factionamt1"
                        />
                    </div>
                </div>
                <div class="rounded-lg border border-base-300 p-3 bg-base-100">
                    <div class="text-sm font-medium mb-2">Faction Mod 2</div>
                    <div class="grid grid-cols-2 gap-3">
                        <div
                            x-data='ajaxSelect({
                                searchUrl: "/factions/search",
                                useModal: false,
                                prefillValue: @json([
                                    "id" => $item->factionmod2,
                                    "name" => ($factions[$item->factionmod2] ?? null)
                                ]),
                                keyInOption: true,
                            })'
                            x-init="init()"
                        >
                            <label class="label">Faction</label>
                            <select
                                x-ref="select"
                                name="factionmod2"
                                class="w-full validator invalid:select-error"
                            ></select>
                        </div>
                        <x-form.input
                            name="factionamt2"
                            label="Amount"
                            type="number"
                            :value="$item->factionamt2"
                        />
                    </div>
                </div>
                <div class="rounded-lg border border-base-300 p-3 bg-base-100">
                    <div class="text-sm font-medium mb-2">Faction Mod 3</div>
                    <div class="grid grid-cols-2 gap-3">
                        <div
                            x-data='ajaxSelect({
                                searchUrl: "/factions/search",
                                useModal: false,
                                prefillValue: @json([
                                    "id" => $item->factionmod3,
                                    "name" => ($factions[$item->factionmod3] ?? null)
                                ]),
                                keyInOption: true,
                            })'
                            x-init="init()"
                        >
                            <label class="label">Faction</label>
                            <select
                                x-ref="select"
                                name="factionmod3"
                                class="w-full validator invalid:select-error"
                            ></select>
                        </div>
                        <x-form.input
                            name="factionamt3"
                            label="Amount"
                            type="number"
                            :value="$item->factionamt3"
                        />
                    </div>
                </div>
                <div class="rounded-lg border border-base-300 p-3 bg-base-100">
                    <div class="text-sm font-medium mb-2">Faction Mod 4</div>
                    <div class="grid grid-cols-2 gap-3">
                        <div
                            x-data='ajaxSelect({
                                searchUrl: "/factions/search",
                                useModal: false,
                                prefillValue: @json([
                                    "id" => $item->factionmod4,
                                    "name" => ($factions[$item->factionmod4] ?? null)
                                ]),
                                keyInOption: true,
                            })'
                            x-init="init()"
                        >
                            <label class="label">Faction</label>
                            <select
                                x-ref="select"
                                name="factionmod4"
                                class="w-full validator invalid:select-error"
                            ></select>
                        </div>
                        <x-form.input
                            name="factionamt4"
                            label="Amount"
                            type="number"
                            :value="$item->factionamt4"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
