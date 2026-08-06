@extends('layouts.app')
@section('title', 'Characters: ' . $character->name)
@section('page-title', 'Character: ' . $character->name)

@section('content')
<div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">
    <div class="bg-base-100 shadow rounded-lg p-6 mb-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold">
                    {{ $character->name }}
                    @if($character->last_name)
                        <span class="text-lg text-muted">{{ $character->last_name }}</span>
                    @endif
                    @if($character->guildMember && $character->guildMember->guild)
                        &lt;<a class="text-base link-info link-hover" href="{{ route('guilds.show', $character->guildMember->guild->id) }}">
                            {{ $character->guildMember->guild->name }}
                        </a>&gt;
                    @endif
                    <span class="text-sm text-muted">#{{ $character->id }}</span>
                </h1>
                <div class="flex items-center gap-3 mt-1 text-sm text-muted">
                    <div>{{ eq_race($character->race) }} • {{ eq_class($character->class) }} • Level {{ $character->level }}</div>
                </div>
            </div>
            {{-- <div class="text-right">
                <a href="{{ route('characters.edit', $character->id) }}" class="btn btn-sm btn-soft">Edit</a>
            </div> --}}
        </div>
    </div>

    <div class="tabs tabs-lift">
        <input type="radio" name="character_tabs" class="tab" aria-label="General" checked="checked" />
        <div class="tab-content bg-base-100 border-base-300 p-6">
            @include('characters.tabs.general')
        </div>

        <input type="radio" name="character_tabs" class="tab" aria-label="Inventory" />
        <div class="tab-content bg-base-100 border-base-300 p-6">
            @include('characters.tabs.inventory')
        </div>

        <input type="radio" name="character_tabs" class="tab" aria-label="Skills" />
        <div class="tab-content bg-base-100 border-base-300 p-6">
            @include('characters.tabs.skills')
        </div>

        @if ($character->spells->isNotEmpty() || $character->disciplines->isNotEmpty())
        <input type="radio" name="character_tabs" class="tab" aria-label="Spells" />
        <div class="tab-content bg-base-100 border-base-300 p-6">
            @include('characters.tabs.spells')
        </div>
        @endif

        <input type="radio" name="character_tabs" class="tab" aria-label="DZ Lockouts" />
        <div class="tab-content bg-base-100 border-base-300 p-6">
            @include('characters.tabs.lockouts')
        </div>

        <input type="radio" name="character_tabs" class="tab" aria-label="Factions" />
        <div class="tab-content bg-base-100 border-base-300 p-6">
            @include('characters.tabs.factions')
        </div>

        <input type="radio" name="character_tabs" class="tab" aria-label="Achievements"
            title="Inspect completion, progress, reward ledgers, selections, and queued mutations" />
        <div class="tab-content bg-base-100 border-base-300 p-6">
            <div class="card bg-base-200 border border-base-content/10">
                <div class="card-body">
                    <h2 class="card-title">Achievement State</h2>
                    <p class="text-sm opacity-70">
                        Open the focused achievement-state editor to inspect this character's durable progress,
                        completion versions, reward delivery ledgers, selections, and queued mutations.
                    </p>
                    <x-ui.alert-warning>
                        State changes are offline database repairs. Live notifications, dependency evaluation, and reward
                        delivery occur only when the character next logs in or reloads achievement state.
                    </x-ui.alert-warning>
                    <div class="card-actions justify-end">
                        <a href="{{ route('characters.achievements.show', $character->id) }}" class="btn btn-soft btn-success"
                            title="Open the safe per-achievement repair workflow for this character">
                            Manage Achievements
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <label class="tab">
            <input type="radio" name="character_tabs" value="corpses"
                @if(request()->get('tab') === 'corpses') checked @endif
            />
            Corpses
            <div class="badge badge-xs badge-soft badge-info ml-2">{{ $character->corpses->count() }}</div>
        </label>
        <div class="tab-content bg-base-100 border-base-300 p-6">
            @if ($character?->corpses->isNotEmpty())
                @include('characters.tabs.corpses')
            @else
                <x-ui.alert-info>
                    Character has no corpses!
                </x-ui.alert-info>
            @endif
        </div>
    </div>

    <x-modal-form x-show="$store.modalForm.isOpen">
        <template x-if="$store.modalForm.activeModal === 'faction'">
            @include('characters.forms.faction')
        </template>
        <template x-if="$store.modalForm.activeModal === 'character-move'">
            @include('characters.forms.move')
        </template>
    </x-modal-form>

</div>
@endsection
