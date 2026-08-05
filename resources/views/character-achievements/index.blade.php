@extends('layouts.app')
@section('title', 'Character Achievements')
@section('page-title', 'Character Achievement Editor')

@section('content')
    <div class="space-y-5">
        <div role="alert" class="alert alert-soft alert-info items-start">
            <div>
                <div class="font-semibold">Choose a character before editing durable achievement state.</div>
                <p class="mt-1 text-sm opacity-80">
                    Completion counts are rows in <code>character_achievements</code>. Progress counts show
                    durable component rows with a positive count; zero-count rows remain visible in the total-row
                    column for diagnostics.
                </p>
            </div>
        </div>

        <x-top-links>
            <x-slot name="left">
                <form method="GET" action="{{ route('character-achievements.index') }}"
                    class="flex flex-wrap items-end gap-2">
                    <label class="form-control w-full sm:w-80">
                        <span class="label py-1">
                            <span class="label-text font-semibold">Character ID or name</span>
                            <span class="label-text-alt tooltip tooltip-left"
                                data-tip="A numeric search checks the exact character ID and also searches names.">?</span>
                        </span>
                        <input type="search" name="q" value="{{ request('q') }}"
                            class="input input-bordered w-full" placeholder="e.g. 42 or Firiona"
                            aria-label="Search by character ID or name">
                    </label>
                    <button type="submit" class="btn btn-accent tooltip"
                        data-tip="Apply the character search filter">Search</button>
                    @if (request()->filled('q'))
                        <a href="{{ route('character-achievements.index') }}" class="btn btn-ghost tooltip"
                            data-tip="Clear the character search">Clear</a>
                    @endif
                </form>
            </x-slot>
            <a href="{{ route('achievements.index') }}" class="btn btn-soft btn-info tooltip"
                data-tip="Open the global achievement definition editor">Achievement Content</a>
        </x-top-links>

        <x-search-results :items="$characters" title="Characters">
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead class="text-xs uppercase bg-neutral text-neutral-content">
                        <tr>
                            <th title="Primary key from character_data.id">Character</th>
                            <th title="Current saved level and class">Level / Class</th>
                            <th title="character_data.ingame; direct offline completion is blocked while this is nonzero">
                                Runtime
                            </th>
                            <th class="text-right" title="Durable completion rows owned by this character">Completed</th>
                            <th class="text-right"
                                title="Progress rows whose current_count is greater than zero">Active Progress</th>
                            <th class="text-right"
                                title="All durable progress rows, including rows whose current_count is zero">Progress Rows</th>
                            <th class="text-right"
                                title="Sum of durable component current_count values; this is diagnostic, not achievement points">
                                Count Total
                            </th>
                            <th title="Last persisted login timestamp">Last Login</th>
                            <th class="text-right" title="Open the scoped character achievement catalog">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($characters as $char)
                            @php
                                $isOnline = (int) ($char->ingame ?? 0) !== 0;
                            @endphp
                            <tr>
                                <td>
                                    <a href="{{ route('characters.achievements.show', $char->id) }}"
                                        class="link link-accent font-semibold">
                                        {{ $char->name }}
                                    </a>
                                    <span class="badge badge-sm badge-ghost ml-1 tooltip"
                                        data-tip="character_data.id">{{ $char->id }}</span>
                                </td>
                                <td>
                                    <span class="font-medium">{{ $char->level }}</span>
                                    <span class="text-base-content/70">{{ eq_class($char->class) }}</span>
                                </td>
                                <td>
                                    @if ($isOnline)
                                        <span class="badge badge-error badge-soft tooltip"
                                            data-tip="The character is marked in game. Offline force-completion is unavailable.">
                                            In game
                                        </span>
                                    @else
                                        <span class="badge badge-success badge-soft tooltip"
                                            data-tip="The character is not marked in game and can use offline-only actions.">
                                            Offline
                                        </span>
                                    @endif
                                </td>
                                <td class="text-right font-mono">
                                    {{ number_format((int) $char->achievement_completion_count) }}
                                </td>
                                <td class="text-right font-mono">
                                    {{ number_format((int) $char->achievement_progress_count) }}
                                </td>
                                <td class="text-right font-mono">
                                    {{ number_format((int) $char->achievement_progress_row_count) }}
                                </td>
                                <td class="text-right font-mono">
                                    {{ number_format((int) $char->achievement_progress_total) }}
                                </td>
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
                                        class="btn btn-sm btn-soft btn-accent tooltip"
                                        data-tip="Inspect and edit only this character's achievement state">
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
