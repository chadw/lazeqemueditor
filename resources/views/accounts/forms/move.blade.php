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
    <div class="card bg-base-200 card-sm shadow-sm mb-45">
        <div class="card-body">
            <div class="grid grid-cols-2 gap-4">
                <div
                    x-data='ajaxSelect({
                        searchUrl: "/accounts/search",
                        useModal: false,
                        prefillValue: @json([
                            "id" => $account->id,
                            "name" => $account->name
                        ]),
                        allowNone: false,
                        keyInOption: true,
                    })'
                    x-init="init()"
                    class="col-span-2"
                >
                    <label class="label">Account to move character to</label>
                    <select
                        x-ref="select"
                        name="account_id"
                        class="w-full validator invalid:select-error"
                    ></select>
                </div>
            </div>
        </div>
    </div>
</div>
