<x-guest-layout>
    <div class="p-8 pb-4 text-center">
        <h1 class="text-headline-xl-mobile md:text-headline-xl text-on-surface mb-2">Set a new password</h1>
        <p class="text-body-md text-secondary">Choose a strong password for your account.</p>
    </div>
    <div class="p-8 pt-4">
        <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">
            <div class="space-y-2">
                <label for="email" class="text-label-bold text-on-surface-variant block">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required class="fni-input" />
                <x-input-error :messages="$errors->get('email')" />
            </div>
            <div class="space-y-2">
                <label for="password" class="text-label-bold text-on-surface-variant block">New password</label>
                <input id="password" type="password" name="password" required autocomplete="new-password" class="fni-input" />
                <x-input-error :messages="$errors->get('password')" />
            </div>
            <div class="space-y-2">
                <label for="password_confirmation" class="text-label-bold text-on-surface-variant block">Confirm password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required class="fni-input" />
            </div>
            <button type="submit" class="w-full py-4 bg-primary-container text-on-primary text-label-bold rounded-lg hover:opacity-90 transition-all flex items-center justify-center gap-2">
                <span class="material-symbols-outlined">lock_reset</span>
                Reset password
            </button>
        </form>
    </div>
</x-guest-layout>
