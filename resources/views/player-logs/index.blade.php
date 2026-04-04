@extends('layouts.app')
@section('page-title', 'Player Event Logs')

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">

        <x-top-links>
            <x-slot name="left">
                @include('player-logs.partials.filters')
            </x-slot>
        </x-top-links>

        <x-search-results :items="$logs" title="Player Event Logs">
            <x-ui.table height="overflow-x-auto max-h-[70vh] overflow-y-auto" theadsticky="top-0 z-10">
                <x-slot:head>
                    <tr>
                        <th scope="col" class="w-[10%]">Character</th>
                        <th scope="col" class="w-[10%]">Zone</th>
                        <th scope="col" class="w-[10%]">Type</th>
                        <th scope="col">Event</th>
                        <th scope="col" class="w-[10%]">Created</th>
                        <th scope="col" class="w-[10%] text-right">-</th>
                    </tr>
                </x-slot:head>
                <x-slot:body>
                    @foreach ($logs as $log)
                        <tr>
                            <td scope="row">
                                <div class="flex flex-col">
                                    <span class="text-xs text-base-content/60">
                                        {{ $log->account->name ?? 'N/A' }}
                                    </span>
                                    <span class="font-medium">
                                        {{ $log->character->name ?? 'Unknown' }}
                                    </span>
                                </div>
                            </td>
                            <td>{{ $log->zone ? $log->zone->short_name : 'N/A' }}</td>
                            <td class="truncate">{{ config('everquest.pel_events.' . $log->event_type_id) }}</td>
                            <td>
                                <x-player-logs.event_types
                                    :log="$log"
                                    :altCurrency="$altCurrency"
                                />
                            </td>
                            <td>
                                {{ $log->created_at
                                    ? \Carbon\Carbon::parse($log->created_at)->format('Y-m-d H:i')
                                    : '' }}
                            </td>
                            <td class="text-right">
                                <div class="inline join">
                                    {{-- <a href="{{ route('player-logs.show', $log) }}" class="join-item btn btn-sm btn-soft">
                            View
                        </a> --}}
                                    <form action="{{ route('player-logs.destroy', $log) }}" method="POST"
                                        class="inline">
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

        <div class="mt-4">{{ $logs->links() }}</div>

    </div>
@endsection
