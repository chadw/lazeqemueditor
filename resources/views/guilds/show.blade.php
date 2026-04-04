@extends('layouts.app')
@section('title', 'Guild: ' . $guild->name)
@section('page-title', 'Guild: ' . $guild->name)

@section('content')
    <div class="flex w-full flex-col">
        <div class="stats shadow space-y-6">
            <div class="stat">
                <div class="stat-figure text-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M10 13a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                        <path d="M8 21v-1a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v1" />
                        <path d="M15 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                        <path d="M17 10h2a2 2 0 0 1 2 2v1" />
                        <path d="M5 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                        <path d="M3 13v-1a2 2 0 0 1 2 -2h2" />
                    </svg>
                </div>
                <div class="stat-title">Members</div>
                <div class="stat-value">{{ $guild->members->count() }}</div>
            </div>

            <div class="stat">
                <div class="stat-figure text-success">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 6l4 6l5 -4l-2 10h-14l-2 -10l5 4z" />
                    </svg>
                </div>
                <div class="stat-title">Leader</div>
                <div class="stat-value">
                    <a class="link-accent link-hover" href="{{ route('characters.show', $guild->leaderCharacter->id) }}">
                        {{ $guild->leaderCharacter->name }}
                    </a>
                </div>
            </div>

            <div class="stat">
                <div class="stat-figure text-warning">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M18 21v-14" />
                        <path d="M9 15l3 -3l3 3" />
                        <path d="M15 10l3 -3l3 3" />
                        <path d="M3 21l18 0" />
                        <path d="M12 21l0 -9" />
                        <path d="M3 6l3 -3l3 3" />
                        <path d="M6 21v-18" />
                    </svg>
                </div>
                <div class="stat-title">Avg Level</div>
                <div class="stat-value">{{ $avgLevel }}</div>
            </div>
        </div>
        @if ($guild->motd)
            <div class="card bg-base-200 card-sm shadow-sm mb-4">
                <div class="card-body">
                    <h3 class="card-title">Message of the Day <span class="text-sm text-accent">(Set by:
                            {{ $guild->motd_setter }})</span></h3>
                    {{ $guild->motd }}
                </div>
            </div>
        @endif
    </div>

    <div class="tabs tabs-lift">
        <input type="radio" name="guild_tabs" class="tab" aria-label="Members" checked="checked" />
        <div class="tab-content bg-base-100 border-base-300 p-6">
            @include('guilds.tabs.members')
        </div>
        <input type="radio" name="guild_tabs" class="tab" aria-label="Permissions" />
        <div class="tab-content bg-base-100 border-base-300 p-6">
            @include('guilds.tabs.permissions')
        </div>
        <input type="radio" name="guild_tabs" class="tab" aria-label="Bank" />
        <div class="tab-content bg-base-100 border-base-300 p-6">
            @include('guilds.tabs.bank')
        </div>
    </div>
@endsection
