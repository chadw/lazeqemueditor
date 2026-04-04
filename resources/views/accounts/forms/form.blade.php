<div class="space-y-6" x-data="formTracker">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="col-span-2">
                <div class="p-4 bg-base-100 border border-base-300 rounded-lg space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="badge badge-neutral badge-sm">
                                #{{ $account->id }}
                            </span>
                            <span class="font-semibold text-base-content">
                                {{ $account->name }}
                            </span>
                        </div>
                        <span class="text-xs text-base-content/60">
                            Account Created:
                            {{ $account->time_creation
                                ? \Carbon\Carbon::parse($account->time_creation)->format('Y-m-d H:i')
                                : 'N/A'
                            }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-2 gap-4">
                <x-form.select
                    name="status"
                    label="Status"
                    :options="config('everquest.account_status')"
                    :selected="$account->status"
                />
                <x-form.select
                    name="flymode"
                    label="Fly Mode"
                    :options="[
                        0 => 'Ground',
                        1 => 'Flying',
                        2 => 'Levitating',
                        3 => 'Water',
                        4 => 'Floating',
                        5 => 'Levitate While Running',
                    ]"
                    :selected="$account->flymode"
                />
                <x-form.textarea
                    name="suspend_reason"
                    label="Suspend Reason"
                    rows="1"
                    class="resize-none"
                    :value="$account->suspend_reason"
                    wrapper-class="col-span-2"
                />
                <x-form.textarea
                    name="ban_reason"
                    label="Ban Reason"
                    rows="1"
                    class="resize-none"
                    :value="$account->ban_reason"
                    wrapper-class="col-span-2"
                />
                <x-form.input
                    name="sharedplat"
                    label="Shared Plat"
                    type="number"
                    min="0"
                    :value="$account->sharedplat"
                />
                <x-form.input
                    name="suspendeduntil"
                    label="Suspended Until"
                    type="datetime-local"
                    step="1"
                    :value="$account->suspendeduntil"
                />
            </div>
        </div>
    </div>
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Options</h2>
            <div class="flex flex-wrap items-center gap-4">
                <x-form.checkbox
                    name="gmspeed"
                    label="GM Speed"
                    :checked="$account->gmspeed"
                />
                <x-form.checkbox
                    name="invulnerable"
                    label="Invulnerable"
                    :checked="$account->invulnerable"
                />
                <x-form.checkbox
                    name="hideme"
                    label="Hide Me"
                    :checked="$account->hideme"
                />
                <x-form.checkbox
                    name="ignore_tells"
                    label="Ignore Tells"
                    :checked="$account->ignore_tells"
                />
                <x-form.checkbox
                    name="revoked"
                    label="OOC Chat Revoked"
                    :checked="$account->revoked"
                />
            </div>
        </div>
    </div>
</div>
