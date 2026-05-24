@extends('layouts.app')
@section('title', 'Edit Loot Table: ' . $table->name)
@section('page-title', 'Edit Loot Table: ' . $table->name)

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">

        <x-top-links>
            <a href="{{ route('loot.index') }}" class="btn btn-soft btn-accent">
                Back to Loot
            </a>
            <button type="button" class="btn btn-soft btn-success"
                @click="$store.modalForm.openCreate({
                baseUrl: '{{ route('loot.drops.store', $table) }}',
                resourceName: 'Loot Drop',
                modal: 'lootdrop',
                defaults: {
                    entry: {
                        multiplier: 1,
                        droplimit: 0,
                        mindrop: 0,
                        probability: 100,
                    }
                }
            })">
                <x-ui.icon name="add" /> Add Loot Drop
            </button>
        </x-top-links>

        @include('loot.partials.loottable-edit', [
            'lt' => $table,
        ])

        @include('loot.partials.loottable-npcs', [
            'table' => $table,
        ])

        <div class="space-y-6">
            @include('loot.partials.lootdrops-edit', [
                'lt' => $table,
            ])
        </div>

        <x-modal-form x-show="$store.modalForm.isOpen">
            <template x-if="$store.modalForm.activeModal === 'loottable-entry'">
                @include('loot.forms.loottable-entry-form')
            </template>
            <template x-if="$store.modalForm.activeModal === 'lootdrop'">
                @include('loot.forms.lootdrop-form')
            </template>
            <template x-if="$store.modalForm.activeModal === 'lootdrop-items'">
                @include('loot.forms.lootdrop-item-form')
            </template>
            <template x-if="$store.modalForm.activeModal === 'lootdrop-tables'">
                @include('loot.drops.partials.modal-loottables')
            </template>
        </x-modal-form>
    </div>
@endsection
