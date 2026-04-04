@extends('layouts.app')
@section('title', 'Profile')
@section('page-title', 'Profile')

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">
        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="card bg-base-200 card-sm shadow-sm">
                    <div class="card-body">
                        <h2 class="card-title">Profile Information</h2>
                        <p class="text-sm text-base-content/70">Update your account details.</p>
                        <div class="mt-4">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>
                </div>

                <div class="card bg-base-200 card-sm shadow-sm">
                    <div class="card-body">
                        <h2 class="card-title">Security</h2>
                        <p class="text-sm text-base-content/70">Change your password to keep your account secure.</p>
                        <div class="mt-4">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>
                </div>

                {{-- <div class="card bg-base-200 card-sm shadow-sm">
                    <div class="card-body">
                        <h2 class="card-title">Danger Zone</h2>
                        <p class="text-sm text-base-content/70">Permanently delete your account.</p>
                        <div class="mt-4">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>
@endsection
