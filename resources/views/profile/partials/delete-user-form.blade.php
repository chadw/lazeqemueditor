<div>
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-semibold">Delete Account</h3>
            <p class="text-sm text-base-content/70">Deleting your account is permanent. All data will be lost.</p>
        </div>

        <div>
            <button x-data x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                class="btn btn-soft btn-error">Delete Account</button>
        </div>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium">Are you sure?</h2>
            <p class="text-sm text-base-content/70 mt-2">This action cannot be undone. Enter your password to confirm
                account deletion.</p>

            <div class="mt-4">
                <label class="label sr-only" for="password">Password</label>
                <input id="password" name="password" type="password" class="input w-full" placeholder="Password" />
                <p class="text-sm text-error">
                    @error('password')
                        {{ $message }}
                    @enderror
                </p>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="btn btn-soft">Cancel</button>
                <button type="submit" class="btn btn-soft btn-error">Delete Account</button>
            </div>
        </form>
    </x-modal>
</div>
