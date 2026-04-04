<form method="post" action="{{ route('password.update') }}" class="space-y-6">
    @csrf
    @method('put')

    <div class="grid grid-cols-1 gap-4">
        <div>
            <label class="label">Current password</label>
            <input id="update_password_current_password" name="current_password" type="password"
                class="input input-bordered w-full" autocomplete="current-password" />
            <p class="text-sm text-error">
                @error('current_password')
                    {{ $message }}
                @enderror
            </p>
        </div>

        <div>
            <label class="label">New password</label>
            <input id="update_password_password" name="password" type="password" class="input input-bordered w-full"
                autocomplete="new-password" />
            <p class="text-sm text-error">
                @error('password')
                    {{ $message }}
                @enderror
            </p>
        </div>

        <div>
            <label class="label">Confirm password</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                class="input input-bordered w-full" autocomplete="new-password" />
            <p class="text-sm text-error">
                @error('password_confirmation')
                    {{ $message }}
                @enderror
            </p>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <button type="submit" class="btn btn-soft btn-success">Save</button>
        @if (session('status') === 'password-updated')
            <p class="text-sm text-success">{{ __('Saved.') }}</p>
        @endif
    </div>
</form>
