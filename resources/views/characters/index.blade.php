@extends('layouts.app')
@section('title', 'Characters')
@section('page-title', 'Characters')

@section('content')
    <div x-data @keydown.window.escape="$store.modalForm.close()">
        <x-top-links>
            <x-slot name="left">
                @include('characters.partials.filters')
            </x-slot>
            {{-- <a href="{{ route('characters.create') }}" class="btn btn-soft btn-success">New Character</a> --}}
        </x-top-links>

        <x-search-results :items="$characters" title="Characters">
            <x-ui.table>
                <x-slot:head>
                    <tr>
                        <x-th-sort field="id" label="ID" class="w-[5%]" />
                        <x-th-sort field="name" label="Name" />
                        <x-th-sort field="account" label="Account" />
                        <th scope="col">Race</th>
                        <x-th-sort field="guild" label="Guild" />
                        <x-th-sort field="birthday" label="Birthday" />
                        <x-th-sort field="last_login" label="Last Login" />
                        <x-th-sort field="time_played" label="Playtime" />
                        <th scope="col" class="text-right w-[10%]">-</th>
                    </tr>
                </x-slot:head>
                <x-slot:body>
                    @forelse ($characters as $char)
                        <tr>
                            <td>{{ $char->id }}</td>
                            <td>
                                <a href="{{ route('characters.show', $char->id) }}" class="link-accent link-hover">{{ $char->name }}</a>
                                <div class="mt-1 flex items-center gap-2">
                                    <span class="text-xs text-base-content/60">Lvl {{ $char->level }} {{ eq_class($char->class) }}</span>
                                </div>
                            </td>
                            <td>
                                @if ($char->account)
                                    <a class="link-info link-hover" href="{{ route('accounts.show', $char->account) }}">{{ $char->account->name }}</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ eq_race($char->race) }}</td>
                            <td>{{ $char->guildMember?->guild?->name ?? '-' }}</td>
                            <td>{{ $char->birthday ? \Carbon\Carbon::createFromTimestamp($char->birthday)->format('Y-m-d') : '-' }}</td>
                            <td>{{ $char->last_login ? \Carbon\Carbon::createFromTimestamp($char->last_login)->format('Y-m-d H:i') : '-' }}</td>
                            <td>{{ isset($char->time_played) ? seconds_to_human($char->time_played) : '-' }}</td>
                            <td class="text-right">
                                <a href="{{ route('characters.show', $char->id) }}" class="btn btn-sm btn-soft btn-accent">
                                    <x-ui.icon name="show" />
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-6 text-base-content/50">
                                No characters found.
                            </td>
                        </tr>
                    @endforelse
                </x-slot:body>
            </x-ui.table>
        </x-search-results>

        <div class="mt-4">{{ $characters->links() }}</div>
    </div>
@endsection
