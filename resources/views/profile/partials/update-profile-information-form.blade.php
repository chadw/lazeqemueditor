<form id="send-verification" method="post" action="{{ route('verification.send') }}">@csrf</form>

<form method="post" action="{{ route('profile.update') }}" class="space-y-6">
    @csrf
    @method('patch')

    <div class="grid grid-cols-1 gap-4">
        <div>
            <label class="label">Name</label>
            <input id="name" name="name" type="text" class="input w-full"
                value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
            <p class="text-sm text-error">
                @error('name')
                    {{ $message }}
                @enderror
            </p>
        </div>

        <div>
            <label class="label">Email</label>
            <input id="email" name="email" type="email" class="input w-full"
                value="{{ old('email', $user->email) }}" required autocomplete="username" />
            <p class="text-sm text-error">
                @error('email')
                    {{ $message }}
                @enderror
            </p>

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                <div class="mt-2">
                    <p class="text-sm text-base-content/70">{{ __('Your email address is unverified.') }}</p>
                    <button form="send-verification"
                        class="btn btn-ghost btn-sm mt-2">{{ __('Re-send verification email') }}</button>
                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm text-success">
                            {{ __('A new verification link has been sent to your email address.') }}</p>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <button type="submit" class="btn btn-soft btn-success">Save</button>
        @if (session('status') === 'profile-updated')
            <p class="text-sm text-success">{{ __('Saved.') }}</p>
        @endif
    </div>
</form>
