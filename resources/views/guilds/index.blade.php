@extends('layouts.app')
@section('title', 'Guilds')
@section('page-title', 'Guilds')

@section('content')
    <x-top-links>
        <x-slot name="left">
            @include('guilds.partials.filters')
        </x-slot>
    </x-top-links>

    <x-search-results :items="$guilds" title="Guilds">
        <x-ui.table>
            <x-slot:head>
                <tr>
                    <x-th-sort field="name" label="Name" class="w-[30%]" />
                    <x-th-sort field="leader" label="Leader" />
                    <x-th-sort field="members_count" label="Members" class="w-[5%]" />
                    <th scope="col" class="w-[10%] text-right">-</th>
                </tr>
            </x-slot:head>
            <x-slot:body>
                @forelse ($guilds as $guild)
                    <tr>
                        <td>
                            <a href="{{ route('guilds.show', $guild) }}"
                                class="text-base link-info link-hover">
                                {{ $guild->name }}
                            </a>
                        </td>
                        <td>{{ $guild->leaderCharacter->name ?? 'Unknown' }}</td>
                        <td>{{ $guild->members_count }}</td>
                        <td class="text-right">
                            <a href="{{ route('guilds.show', $guild) }}"
                                class="btn btn-sm btn-soft btn-accent">
                                <x-ui.icon name="show" />
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-gray-500">
                            No guilds found.
                        </td>
                    </tr>
                @endforelse
            </x-slot:body>
        </x-ui.table>
    </x-search-results>

    <div class="mt-4">{{ $guilds->links() }}</div>

@endsection
