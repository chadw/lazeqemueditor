@extends('layouts.app')
@section('title', 'Edit Spawn Group: ' . $spawngroup->name)
@section('page-title', 'Edit Spawn Group: ' . $spawngroup->name)

@section('content')
    <div x-data>

        <x-top-links>
            <a href="{{ route('spawngroups.index') }}" class="btn btn-soft btn-accent">
                Back to Spawn Groups
            </a>
            <button type="button" class="btn btn-soft btn-success float-end"
                @click="$store.modalForm.openCreate({
                    baseUrl: '{{ route('spawngroups.store') }}',
                    resourceName: 'Spawn Group'
                })"
            >
                <x-ui.icon name="add" /> New Spawn Group
            </button>
        </x-top-links>

        <form method="POST" action="{{ route('spawngroups.update', $spawngroup) }}">
            @csrf
            @method('PATCH')
            @include('spawngroups.forms.spawngroup')
        </form>

        @include('spawngroups.partials.spawns', ['spawngroup' => $spawngroup])

        <x-modal-form x-show="$store.modalForm.isOpen">
            <template x-if="$store.modalForm.activeModal === 'spawn-group'">
                @include('spawngroups.forms.spawngroup')
            </template>
            <template x-if="$store.modalForm.activeModal === 'spawn-entry'">
                @include('spawngroups.forms.spawnentry')
            </template>
            <template x-if="$store.modalForm.activeModal === 'spawn-point'">
                @include('spawngroups.forms.spawnpoint')
            </template>
        </x-modal-form>
    </div>
@endsection
