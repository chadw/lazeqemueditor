<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

    <div class="card bg-base-200 shadow-sm">
        <div class="card-body">
            <h3 class="card-title text-sm uppercase opacity-70">Identity</h3>

            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="opacity-60">Account Name</span>
                    <div class="font-semibold">{{ $account->name }}</div>
                </div>
                <div>
                    <span class="opacity-60">Last Played Character</span>
                    <div class="font-semibold">{{ $account->charname }}</div>
                </div>
                <div>
                    <span class="opacity-60">Created</span>
                    <div class="font-semibold">
                        {{ $account->time_creation
                            ? \Carbon\Carbon::parse($account->time_creation)->format('Y-m-d H:i')
                            : ''
                        }}
                    </div>
                </div>
                <div>
                    <span class="opacity-60">Login Server ID</span>
                    <div class="font-semibold">{{ $account->lsaccount_id }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-200 shadow-sm">
        <div class="card-body">
            <h3 class="card-title text-sm uppercase opacity-70">Economy & Meta</h3>

            <div class="stats stats-vertical lg:stats-horizontal bg-base-100">
                <div class="stat">
                    <div class="stat-title">Shared Plat</div>
                    <div class="stat-value text-success">
                        {{ number_format($account->sharedplat) }}
                    </div>
                </div>

                <div class="stat">
                    <div class="stat-title">Karma</div>
                    <div class="stat-value">
                        {{ $account->karma }}
                    </div>
                </div>

                <div class="stat">
                    <div class="stat-title">Status</div>
                    <div class="stat-value text-sm">
                        <span class="badge badge-soft badge-info">
                            {{ $account->status }}
                        </span>
                        {{ config('everquest.account_status')[$account->status] ?? 'Unknown' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-200 shadow-sm">
        <div class="card-body">
            <h3 class="card-title text-sm uppercase opacity-70">Flags & Abilities</h3>

            <div class="grid grid-cols-2 gap-3 gap-x-16 text-sm">
                @php
                    $flags = [
                        'GM Speed' => $account->gmspeed,
                        'Invulnerable' => $account->invulnerable,
                        'Fly Mode' => $account->flymode,
                        'Ignore Tells' => $account->ignore_tells,
                        'Hide Me' => $account->hideme,
                        'Rules Flag' => $account->rulesflag,
                    ];
                @endphp

                @foreach ($flags as $label => $value)
                    <div class="flex items-center justify-between">
                        <span>{{ $label }}</span>
                        <span class="status {{ $value ? 'status-success' : 'status-error' }}"></span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card bg-base-200 shadow-sm">
        <div class="card-body">
            <h3 class="card-title text-sm uppercase opacity-70">Discipline</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span>OOC Revoked</span>
                    <span class="status {{ $account->revoked ? 'status-success' : 'status-error' }}"></span>
                </div>
                <div>
                    <span class="opacity-60">Suspended Until</span>
                    <div class="font-medium">
                        {{ $account->suspendeduntil ?? 'Never' }}
                    </div>
                </div>
                @if ($account->ban_reason)
                    <div class="alert alert-error text-sm">
                        <strong>Ban Reason:</strong>
                        {{ $account->ban_reason }}
                    </div>
                @endif
                @if ($account->suspend_reason)
                    <div class="alert alert-warning text-sm">
                        <strong>Suspend Reason:</strong>
                        {{ $account->suspend_reason }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="divider"></div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach ($account->characters as $char)
        <div class="card bg-base-200 shadow-md hover:shadow-lg transition">
            <div class="card-body p-4">
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <span
                            class="item-icon item-{{ config('everquest.classes_icons.' . $char->class) }} text-4xl">
                        </span>
                    </div>
                    <div class="flex-1">
                        <span class="badge badge-soft badge-accent absolute top-5 right-4">
                            Lvl {{ $char->level }}
                        </span>
                        <h2 class="text-lg font-bold tracking-wide">
                            <a href="{{ route('characters.show', $char->id) }}" class="link-accent link-hover">
                                {{ $char->name }}
                            </a>
                        </h2>
                        <p class="text-sm opacity-70">
                            {{ config('everquest.races.' . $char->race) }}
                            •
                            {{ config('everquest.classes.' . $char->class) }}
                        </p>
                    </div>
                </div>

                <div class="divider my-2"></div>

                <div class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
                    <div>
                        <span class="opacity-60">Birthday</span><br>
                        <span class="font-medium">
                            {{ $char->birthday
                                ? \Carbon\Carbon::parse($char->birthday)->format('Y-m-d H:i')
                                : 'Unknown'
                            }}
                        </span>
                    </div>
                    <div>
                        <span class="opacity-60">Time Played</span><br>
                        <span class="font-medium">
                            {{ seconds_to_human($char->time_played) }}
                        </span>
                    </div>
                    <div class="col-span-2">
                        <span class="opacity-60">Last Login</span><br>
                        <span class="font-medium">
                            {{ $char->last_login
                                ? \Carbon\Carbon::parse($char->last_login)->format('Y-m-d H:i')
                                : 'Never'
                            }}
                        </span>
                    </div>
                </div>
                <div class="card-actions justify-end mt-4">
                    <button type="button" class="join-item btn btn-sm btn-soft btn-info tooltip"
                        data-tip="Edit"
                        @click="$store.modalForm.openEdit(
                        '',
                        '{{ route('characters.move', $char->id) }}',
                        {
                            modal: 'character-move',
                            resourceName: 'Move Character'
                        }
                    )">
                        <x-ui.icon name="move" /> Move
                    </button>
                    <a href="{{ route('characters.show', $char->id) }}" class="btn btn-sm btn-soft btn-accent">
                        <x-ui.icon name="show" />
                    </a>
                    {{-- <form action="{{ route('accounts.destroy', $account) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button class="join-item btn btn-sm btn-soft btn-error" onclick="return confirm('Delete?')">
                            <x-ui.icon name="delete" />
                        </button>
                    </form> --}}
                </div>
            </div>
        </div>
    @endforeach
</div>
