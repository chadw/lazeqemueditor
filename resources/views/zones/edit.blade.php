@extends('layouts.app')
@section('title', 'Edit Zones')
@section('page-title', 'Edit Zones')

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">
        <x-top-links>
            <x-slot name="left">
                @include('zones.partials.filters')
            </x-slot>
        </x-top-links>

        @if ($zone)
            @include('zones.partials.tabs')

            <x-modal-form x-show="$store.modalForm.isOpen">
                <template x-if="$store.modalForm.activeModal === 'zone-point'">
                    @include('zones.forms.zone-points')
                </template>
                <template x-if="$store.modalForm.activeModal === 'blocked-spell'">
                    @include('zones.forms.blocked-spells')
                </template>
                <template x-if="$store.modalForm.activeModal === 'door'">
                    @include('zones.forms.doors')
                </template>
                <template x-if="$store.modalForm.activeModal === 'ground-spawn'">
                    @include('zones.forms.ground-spawns')
                </template>
                <template x-if="$store.modalForm.activeModal === 'fishing'">
                    @include('zones.forms.fishing')
                </template>
                <template x-if="$store.modalForm.activeModal === 'forage'">
                    @include('zones.forms.forage')
                </template>
                <template x-if="$store.modalForm.activeModal === 'trap'">
                    @include('zones.forms.traps')
                </template>
                <template x-if="$store.modalForm.activeModal === 'objects'">
                    @include('zones.forms.objects')
                </template>
            </x-modal-form>
        @endif

        @include('partials.modal-objects')

    </div>
@endsection
