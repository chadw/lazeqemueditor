@extends('layouts.app')
@section('title', 'Accounts')
@section('page-title', 'Accounts')

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">

        <x-top-links>
            <x-slot name="left">
                @include('accounts.partials.filters')
            </x-slot>
        </x-top-links>

        <x-search-results :items="$accounts" title="Accounts">
            <x-ui.table height="overflow-x-auto max-h-[70vh] overflow-y-auto" theadsticky="top-0 z-10">
                <x-slot:head>
                    <tr>
                        <th scope="col" class="w-[5%]">@sortablelink('id', 'ID')</th>
                        <th scope="col">@sortablelink('name', 'Name')</th>
                        <th scope="col" class="w-[15%]">@sortablelink('status', 'Status')</th>
                        <th scope="col" class="w-[15%]">@sortablelink('time_creation', 'Created')</th>
                        <th scope="col" class="w-[10%] text-right">-</th>
                    </tr>
                </x-slot:head>
                <x-slot:body>
                    @foreach($accounts as $account)
                        <tr
                            @class([
                                'bg-error/25 hover:bg-error/30' => $account->status == -2,
                                'bg-error/10 hover:bg-error/15' => $account->status == -1,
                            ])
                        >
                            <td>{{ $account->id }}</td>
                            <td>
                                <div class="flex flex-col">
                                    <span class="font-medium">{{ $account->name }}</span>

                                    @if ($account->character && $account->character->name)
                                        <span class="text-xs text-base-content/60">
                                            {{ $account->character->name }}
                                            • Lvl {{ $account->character->level }}
                                            • {{ config('everquest.classes_abbr')[$account->character->class] ?? 'Unknown' }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium">
                                            {{ config('everquest.account_status')[$account->status] ?? 'Unknown' }}
                                        </span>
                                    </div>
                                    @if ($account->status == -1)
                                        <div class="text-xs text-base-content/70 space-y-0.5">
                                            <div>
                                                <span class="font-medium">Until:</span>
                                                {{ \Carbon\Carbon::parse($account->suspendeduntil)->format('Y-m-d H:i') }}
                                            </div>

                                            @if ($account->suspend_reason)
                                                <div class="text-base-content/60">
                                                    {{ $account->suspend_reason }}
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                    @if ($account->status == -2 && $account->ban_reason)
                                        <div class="text-xs text-base-content/70">
                                            <span class="font-medium">Reason:</span>
                                            <span class="text-base-content/60">
                                                {{ $account->ban_reason }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                {{ $account->time_creation
                                    ? \Carbon\Carbon::parse($account->time_creation)->format('Y-m-d H:i')
                                    : ''
                                }}
                            </td>
                            <td class="text-right">
                                <div class="join">
                                    <a href="{{ route('accounts.show', $account) }}"
                                        class="btn btn-sm btn-soft btn-accent join-item">
                                        <x-ui.icon name="show" />
                                    </a>
                                    <form action="{{ route('accounts.destroy', $account) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button class="join-item btn btn-sm btn-soft btn-error"
                                            onclick="return confirm('Delete?')">
                                            <x-ui.icon name="delete" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </x-slot:body>
            </x-ui.table>
        </x-search-results>

        <div class="mt-4">{{ $accounts->links() }}</div>

    </div>
@endsection
