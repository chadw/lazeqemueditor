@extends('layouts.app')
@section('title', 'Edit Factions')
@section('page-title', 'Edit Factions')

@section('content')
    <div x-data>
        <x-top-links>
            <x-slot name="left">
                @include('factions.partials.filters')
            </x-slot>
            @if ($faction)
            <div class="dropdown dropdown-bottom dropdown-end dropdown-hover">
                <div tabindex="0" role="button" class="btn btn-soft btn-info">NPC</div>
                <ul tabindex="-1" class="dropdown-content menu bg-base-100 rounded-box z-1 w-60 p-2 shadow-sm">
                    <li><a href="{{ route('factions.npcs.primary', $faction) }}">
                            NPCs on this faction</a>
                    </li>
                    <li><a href="{{ route('factions.npcs.effect', [$faction, 'increase']) }}">
                            NPCs that increase this faction</a>
                    </li>
                    <li><a href="{{ route('factions.npcs.effect', [$faction, 'decrease']) }}">
                            NPCs that decrease this faction</a>
                    </li>
                    <li><a href="{{ route('factions.npcs.effect', [$faction, 'nochange']) }}">
                            NPCs with no faction change</a>
                    </li>
                </ul>
            </div>
            @endif
            <button type="button" class="btn btn-soft btn-success"
                @click="$store.modalForm.openCreate({
                    baseUrl: '{{ route('factions.store') }}',
                    resourceName: 'Faction',
                    modal: 'new',
                    defaults: {
                        base: 0,
                        faction_base_data: {
                            min: -2000,
                            max: 2000,
                            unk_hero1: 0,
                            unk_hero2: 0,
                            unk_hero3: 0,
                        }
                    }
                })"
            >
                <x-ui.icon name="add" /> New Faction
            </button>
            @if ($faction)
            <form action="{{ route('factions.destroy', $faction) }}" method="POST" class="inline">
                @csrf @method('DELETE')
                <button class="btn btn-soft btn-error tooltip" data-tip="Delete"
                    onclick="return confirm('Delete?')">
                    <x-ui.icon name="delete" />
                </button>
            </form>
            @endif
        </x-top-links>

        @if ($faction)
        <form method="POST" action="{{ route('factions.update', $faction) }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="factions" value="1" />
            @include('factions.forms.form', ['faction' => $faction])
        </form>

        @include('factions.partials.index-mods', ['faction' => $faction])
        @endif

        <x-modal-form x-show="$store.modalForm.isOpen">
            <template x-if="$store.modalForm.activeModal === 'new'">
                @include('factions.forms.new')
            </template>
            <template x-if="$store.modalForm.activeModal === 'mod'">
                @include('factions.forms.mods')
            </template>
        </x-modal-form>
    </div>
@endsection
