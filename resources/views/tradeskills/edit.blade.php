@extends('layouts.app')
@section('page-title', 'Tradeskill Recipe: ' . $recipe->name)

@php
    $recipe->flags = $recipe->learn_flags;
@endphp

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">

        <x-top-links>
            <x-slot name="left">
                @include('tradeskills.partials.filters')
            </x-slot>
            <button type="button" class="btn btn-soft btn-success"
                @click="$store.modalForm.openCreate({
                baseUrl: '{{ route('tradeskills.store') }}',
                resourceName: 'Recipe',
                modal: 'tradeskill',
                defaults: {
                    skillneeded: 0,
                    trivial: 0,
                    enabled: true,
                }
            })">
                <x-ui.icon name="add" /> New Recipe
            </button>
            <button type="button" class="btn btn-soft"
                @click="$store.modalForm.openEdit(
                {{ $recipe->toJson() }},
                '{{ route('tradeskills.update', $recipe) }}',
                {
                    modal: 'tradeskill',
                    title: 'Edit Recipe',
                    resourceName: 'Recipe',
                }
            )">
                <x-ui.icon name="edit" />
            </button>
            <form action="{{ route('tradeskills.destroy', $recipe) }}" method="POST" class="inline"
                onsubmit="return confirm('Delete this recipe?')">
                @csrf
                @method('DELETE')
                <button class="btn btn-soft btn-error">
                    <x-ui.icon name="delete" />
                </button>
            </form>
        </x-top-links>

    </div>

    <div class="space-y-6" x-data>
        {{-- containers --}}
        <x-ui.card>
            <x-slot:header>
                <h3 class="card-title">Containers</h3>
                <button type="button" class="btn btn-sm btn-soft btn-success"
                    @click="$store.modalForm.openCreate({
                        baseUrl: '{{ route('tradeskills.entries.store', ['recipe' => $recipe->id]) }}',
                        resourceName: 'Container',
                        modal: 'tradeskill-entries',
                        defaults: {
                            recipe_id: {{ $recipe->id }},
                            componentcount: 0,
                            failcount: 0,
                            salvagecount: 0,
                            successcount: 0,
                            iscontainer: true,
                        }
                })">
                    <x-ui.icon name="add" /> Add
                </button>
            </x-slot:header>
            <div>
                @include('tradeskills.partials.entries-table', [
                    'entries' => $recipe->resolvedContainerEntries(),
                    'type' => 'container'
                ])
            </div>
            <x-slot:footer>
                <div></div>
                <button type="button" class="btn btn-xs btn-soft btn-success"
                    @click="$store.modalForm.openCreate({
                        baseUrl: '{{ route('tradeskills.entries.store', ['recipe' => $recipe->id]) }}',
                        resourceName: 'Container',
                        modal: 'tradeskill-entries',
                        defaults: {
                            recipe_id: {{ $recipe->id }},
                            componentcount: 0,
                            failcount: 0,
                            salvagecount: 0,
                            successcount: 0,
                            iscontainer: true,
                        }
                })">
                    <x-ui.icon name="add" /> Add
                </button>
            </x-slot:footer>
        </x-ui.card>

        {{-- components --}}
        <x-ui.card>
            <x-slot:header>
                <h3 class="card-title">Components</h3>
                <button type="button" class="btn btn-sm btn-soft btn-success"
                    @click="$store.modalForm.openCreate({
                        baseUrl: '{{ route('tradeskills.entries.store', ['recipe' => $recipe->id]) }}',
                        resourceName: 'Component',
                        modal: 'tradeskill-entries',
                        defaults: {
                            recipe_id: {{ $recipe->id }},
                            componentcount: 1,
                            failcount: 0,
                            salvagecount: 0,
                            successcount: 0,
                        }
                })">
                    <x-ui.icon name="add" /> Add
                </button>
            </x-slot:header>
            <div>
                @include('tradeskills.partials.entries-table', [
                    'entries' => $recipe->componentEntriesWithFlags(),
                    'type' => 'component'
                ])
            </div>
            <x-slot:footer>
                <div></div>
                <button type="button" class="btn btn-xs btn-soft btn-success"
                    @click="$store.modalForm.openCreate({
                        baseUrl: '{{ route('tradeskills.entries.store', ['recipe' => $recipe->id]) }}',
                        resourceName: 'Component',
                        modal: 'tradeskill-entries',
                        defaults: {
                            recipe_id: {{ $recipe->id }},
                            componentcount: 1,
                            failcount: 0,
                            salvagecount: 0,
                            successcount: 0,
                        }
                })">
                    <x-ui.icon name="add" /> Add
                </button>
            </x-slot:footer>
        </x-ui.card>

        {{-- results --}}
        <x-ui.card>
            <x-slot:header>
                <h3 class="card-title">Results</h3>
                <button type="button" class="btn btn-sm btn-soft btn-success"
                    @click="$store.modalForm.openCreate({
                        baseUrl: '{{ route('tradeskills.entries.store', ['recipe' => $recipe->id]) }}',
                        resourceName: 'Result',
                        modal: 'tradeskill-entries',
                        defaults: {
                            recipe_id: {{ $recipe->id }},
                            componentcount: 0,
                            failcount: 0,
                            salvagecount: 0,
                            successcount: 1,
                            iscontainer: false,
                        }
                })">
                    <x-ui.icon name="add" /> Add
                </button>
            </x-slot:header>
            <div>
                @include('tradeskills.partials.entries-table', [
                    'entries' => $recipe->successEntries(),
                    'type' => 'success'
                ])
            </div>
            <x-slot:footer>
                <div></div>
                <button type="button" class="btn btn-sm btn-soft btn-success"
                    @click="$store.modalForm.openCreate({
                        baseUrl: '{{ route('tradeskills.entries.store', ['recipe' => $recipe->id]) }}',
                        resourceName: 'Result',
                        modal: 'tradeskill-entries',
                        defaults: {
                            recipe_id: {{ $recipe->id }},
                            componentcount: 0,
                            failcount: 0,
                            salvagecount: 0,
                            successcount: 1,
                            iscontainer: false,
                        }
                })">
                    <x-ui.icon name="add" /> Add
                </button>
            </x-slot:footer>
        </x-ui.card>

        <x-modal-form x-show="$store.modalForm.isOpen">
            <template x-if="$store.modalForm.activeModal === 'tradeskill'">
                @include('tradeskills.forms.form')
            </template>
            <template x-if="$store.modalForm.activeModal === 'tradeskill-entries'">
                @include('tradeskills.forms.entry-form')
            </template>
        </x-modal-form>
    </div>
@endsection
