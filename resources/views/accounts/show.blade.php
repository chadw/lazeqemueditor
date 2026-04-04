@extends('layouts.app')
@section('title', 'Account: ' . $account->name)
@section('page-title', 'Account: ' . $account->name)

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">
        <x-top-links>
            <a href="{{ route('accounts.index') }}" class="btn btn-soft btn-accent">
                Accounts
            </a>
            <button type="button" class="join-item btn btn-soft tooltip" data-tip="Edit"
                @click="$store.modalForm.openEdit(
                    '',
                    '{{ route('accounts.update', $account) }}',
                    {
                        modal: 'account-edit',
                        resourceName: 'Edit Account'
                    }
                )">
                <x-ui.icon name="edit" />
            </button>
            <form action="{{ route('accounts.destroy', $account) }}" method="POST" class="inline">
                @csrf @method('DELETE')
                <button class="join-item btn btn-soft btn-error"
                    onclick="return confirm('Delete?')">
                    <x-ui.icon name="delete" />
                </button>
            </form>
        </x-top-links>

        <div class="tabs tabs-lift">
            <input type="radio" name="guild_tabs" class="tab" aria-label="Details" checked="checked" />
            <div class="tab-content bg-base-100 border-base-300 p-6">
                @include('accounts.tabs.details')
            </div>
            <input type="radio" name="guild_tabs" class="tab" aria-label="Ips" />
            <div class="tab-content bg-base-100 border-base-300 p-6">
                @include('accounts.tabs.ips')
            </div>
            <input type="radio" name="guild_tabs" class="tab" aria-label="Shared Bank" />
            <div class="tab-content bg-base-100 border-base-300 p-6">
                @include('accounts.tabs.sharedbank')
            </div>
        </div>

        <x-modal-form x-show="$store.modalForm.isOpen" width="max-w-3xl">
            <template x-if="$store.modalForm.activeModal === 'account-edit'">
                @include('accounts.forms.form')
            </template>
            <template x-if="$store.modalForm.activeModal === 'character-move'">
                @include('accounts.forms.move')
            </template>
        </x-modal-form>
    </div>
@endsection
