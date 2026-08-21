@extends('layouts.app')
@section('title', 'Character Achievements')
@section('page-title', 'Character Achievement Editor')

@section('content')
    <div class="space-y-5">
        <x-top-links>
            <x-slot name="left">
                @include('character-achievements.partials.filters2')
            </x-slot>
            <a href="{{ route('achievements.index') }}" class="btn btn-soft btn-info">Achievement Content</a>
        </x-top-links>

        <x-search-results :items="$characters" title="Characters">
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead class="text-xs uppercase bg-neutral text-neutral-content">
                        <tr>
                            <th scope="col" class="w-[5%]">Char ID</th>
                            <th scope="col">Character</th>
                            <th scope="col" class="w-[10%]">Runtime</th>
                            <th scope="col" class="w-[5%]">Completed</th>
                            <th scope="col" class="w-[5%]">Active</th>
                            <th scope="col" class="w-[5%]">Progress</th>
                            <th scope="col" class="w-[5%]">Total</th>
                            <th scope="col">Last Login</th>
                            <th scope="col" class="text-right w-[10%]">-</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($characters as $char)
                            @php
                                $isOnline = (int) ($char->ingame ?? 0) !== 0;
                            @endphp
                            <tr>
                                <td>{{ $char->id }}</td>
                                <td>
                                    <a href="{{ route('characters.show', $char->id) }}" class="link-accent link-hover">{{ $char->name }}</a>
                                    <div class="mt-1 flex items-center gap-2">
                                        <span class="text-xs text-base-content/60">Lvl {{ $char->level }} {{ eq_class($char->class) }}</span>
                                    </div>
                                </td>
                                <td>
                                    @if ($isOnline)
                                        <span class="badge badge-success badge-soft tooltip"
                                            data-tip="The character is marked in game. Offline force-completion is unavailable.">
                                            In game
                                        </span>
                                    @else
                                        <span class="badge badge-error badge-soft tooltip"
                                            data-tip="The character is not marked in game and can use offline-only actions.">
                                            Offline
                                        </span>
                                    @endif
                                </td>
                                <td>{{ number_format((int) $char->achievement_completion_count) }}</td>
                                <td>{{ number_format((int) $char->achievement_progress_count) }}</td>
                                <td>{{ number_format((int) $char->achievement_progress_row_count) }}</td>
                                <td>{{ number_format((int) $char->achievement_progress_total) }}</td>
                                <td>
                                    @if ((int) ($char->last_login ?? 0) > 0)
                                        <span class="tooltip"
                                            data-tip="Unix timestamp {{ (int) $char->last_login }}">
                                            {{ \Carbon\Carbon::createFromTimestamp((int) $char->last_login)->format('Y-m-d H:i') }}
                                        </span>
                                    @else
                                        <span class="text-base-content/50" title="No saved login timestamp">Never</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('characters.achievements.show', $char->id) }}"
                                        class="btn btn-sm btn-soft btn-accent">
                                        Manage
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-8 text-center text-base-content/60">
                                    No characters match this search.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-search-results>

        @if ($characters->hasPages())
            <div>{{ $characters->links() }}</div>
        @endif
    </div>
@endsection
