<div class="space-y-6" x-data="formTracker">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="col-span-2">
                <div class="p-4 bg-base-100 border border-base-300 rounded-lg space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="badge badge-neutral badge-sm">#{{ $character->id }}</span>
                            <span class="font-semibold text-base-content">{{ $character->name }}</span>
                        </div>
                        <span class="text-xs text-base-content/60">Current Zone: {{ optional($character->zone)->short_name ?? $character->zone_id }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <x-form.select
                        name="zone_id"
                        label="Zone"
                        :options="$zones"
                        x-model="$store.modalForm.form.zone_id"
                    />
                </div>
                <p>
                    Character will be moved to the zone's safe coordinates.
                </p>
            </div>
        </div>
    </div>
</div>
