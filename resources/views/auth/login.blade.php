@extends('layouts.auth')

@section('content')

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input
                id="email"
                class="mt-1 w-full"
                type="email" name="email"
                :value="old('email')"
                required autofocus autocomplete="username"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <div class="flex justify-between items-center mb-1">
                <x-input-label for="password" :value="__('Password')" />
                {{-- @if (Route::has('password.request'))
                    <a class="text-sm link link-primary" href="{{ route('password.request') }}">{{ __('Forgot your password?') }}</a>
                @endif --}}
            </div>
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="checkbox checkbox-xs" name="remember">
                <span class="ms-2 text-sm text-muted">{{ __('Remember me') }}</span>
            </label>

            <x-primary-button>
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
@endsection
