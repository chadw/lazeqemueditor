@extends('layouts.app')
@section('title', 'AA: ' . $ability->name)
@section('page-title', 'AA: ' . $ability->name)

@section('content')

    <x-top-links>
        <x-slot name="left">
            @include('aa.partials.filters')
        </x-slot>
        <form method="POST" action="{{ route('aa.clone', $ability) }}" class="inline-block">
            @csrf
            <input type="hidden" name="redirect" value="edit" />
            <button type="submit" class="btn btn-soft btn-info tooltip" data-tip="Clone">
                <x-ui.icon name="clone" /> Clone
            </button>
        </form>
        <a href="{{ route('aa.create') }}" class="btn btn-soft btn-success">
            <x-ui.icon name="add" /> New AA
        </a>
        <form action="{{ route('aa.destroy', $ability) }}" method="POST" class="inline">
            @csrf @method('DELETE')
            <button class="join-item btn btn-soft btn-error tooltip" data-tip="Delete"
                onclick="return confirm('Delete?')">
                <x-ui.icon name="delete" /> Delete
            </button>
        </form>
    </x-top-links>

    <div class="grid grid-cols-1 gap-6">
        <form method="POST" action="{{ route('aa.update', $ability) }}">
            @csrf
            @method('PUT')

            @include('aa.forms.ability')

            <div class="flex justify-end gap-2 mt-6">
                <button type="submit" class="btn btn-sm btn-soft btn-success">
                    <x-ui.icon name="save" /> Save Ability
                </button>
            </div>
        </form>
    </div>

    <div class="divider"></div>

    <div x-data="{ allOpen: false }">
        <div class="flex items-center justify-between mb-3">
            <h3 class="font-bold">Manage Ranks</h3>
            <div class="flex items-center gap-2">
                <button type="button" class="btn btn-sm btn-soft btn-success" data-action="ranks-save-all"
                    @click="window.dispatchEvent(new CustomEvent('ranks:saveAll'))">
                    <x-ui.icon name="save" /> Save All Ranks
                </button>
                <button type="button" class="btn btn-sm btn-soft btn-success"
                    @click="$store.modalForm.openCreate({
                        baseUrl: '{{ route('aa.ranks.store', $ability) }}',
                        modal: 'aa-rank',
                        resourceName: 'AA Rank'
                    })
                ">
                    <x-ui.icon name="add" /> Add Rank
                </button>
                <button type="button" class="btn btn-sm btn-soft btn-accent"
                    @click="allOpen = !allOpen; window.dispatchEvent(new CustomEvent('ranks:toggle', {
                        detail: allOpen
                    }))
                ">
                    <span x-text="allOpen ? 'Collapse All' : 'Expand All'"></span>
                </button>
            </div>
        </div>

        <div data-spa-defs='@json(config('eqemu_spa_defs'))'>
            <div x-data="{ ready: false }" x-init="$nextTick(() => ready = true)" class="relative">
                {{-- Skeleton shown while ready == false --}}
                <div x-show="!ready" class="space-y-2">
                    @for ($i = 0; $i < $rankCount; $i++)
                        <div class="collapse collapse-arrow bg-base-200 rounded-box border border-base-100 animate-pulse">
                            <div
                                class="collapse-title flex items-center justify-between gap-4 bg-base-100 border-b border-base-100">
                                <div class="flex items-center gap-4 truncate w-full">
                                    <span class="w-20 h-5 bg-base-300 rounded"></span>
                                    <span class="w-20 h-4 bg-base-300 rounded ml-2"></span>
                                    <span class="w-20 h-4 bg-base-300 rounded ml-2"></span>
                                    <span class="flex-1 h-4 bg-base-300 rounded ml-2"></span>
                                    <span class="w-20 h-4 bg-base-300 rounded ml-2"></span>
                                    <span class="w-20 h-4 bg-base-300 rounded ml-2"></span>
                                </div>
                            </div>
                            <div class="collapse-content">
                                <div class="card bg-base-200 shadow p-4">
                                    <div class="h-6 bg-base-300 rounded w-1/3 mb-2"></div>
                                    <div class="h-4 bg-base-300 rounded w-full mb-1"></div>
                                    <div class="h-4 bg-base-300 rounded w-full"></div>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>

                {{-- Actual content shown when ready == true --}}
                <div x-show="ready" class="space-y-2">
                @foreach ($allRanks as $rank)
                    <x-aa.rank-card
                        :rank="$rank"
                        :index="$loop->iteration"
                        :can-delete="$loop->last"
                    />
                @endforeach
                </div>
            </div>
        </div>

        <x-modal-form x-show="$store.modalForm.isOpen">
            <template x-if="$store.modalForm.activeModal === 'aa-rank'">
                @include('aa.forms.ranks-new')
            </template>
            <template x-if="$store.modalForm.activeModal === 'aa-rank-effect'">
                @include('aa.forms.rank-effects')
            </template>
            <template x-if="$store.modalForm.activeModal === 'aa-rank-prereq'">
                @include('aa.forms.rank-prereqs')
            </template>
        </x-modal-form>

        @include('partials.modal-dbstr-picker')

    </div>
@endsection
