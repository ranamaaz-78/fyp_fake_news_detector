<x-guest-layout>
    <div class="p-8 pb-4 text-center">
        <h1 class="text-headline-xl-mobile md:text-headline-xl text-on-surface mb-2">Create your account</h1>
        <p class="text-body-md text-secondary">Join FNI to save and manage your verification history.</p>
    </div>
    <div class="p-8 pt-4">
        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf
            <div class="space-y-2">
                <label for="name" class="text-label-bold text-on-surface-variant block">Full name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="fni-input" />
                <x-input-error :messages="$errors->get('name')" />
            </div>
            <div class="space-y-2">
                <label for="username" class="text-label-bold text-on-surface-variant block">Username</label>
                <input id="username" type="text" name="username" value="{{ old('username') }}" required autocomplete="username" class="fni-input" />
                <x-input-error :messages="$errors->get('username')" />
            </div>
            <div class="space-y-2">
                <label for="email" class="text-label-bold text-on-surface-variant block">Email address</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline">mail</span>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" class="fni-input pl-10" />
                </div>
                <x-input-error :messages="$errors->get('email')" />
            </div>
            <div class="space-y-2">
                <label for="password" class="text-label-bold text-on-surface-variant block">Password</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline">lock</span>
                    <input id="password" type="password" name="password" required autocomplete="new-password" class="fni-input pl-10" />
                </div>
                <x-input-error :messages="$errors->get('password')" />
            </div>
            <div class="space-y-2">
                <label for="password_confirmation" class="text-label-bold text-on-surface-variant block">Confirm password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="fni-input" />
            </div>
            <button type="submit" class="w-full py-4 bg-primary-container text-on-primary text-label-bold rounded-lg hover:opacity-90 active:scale-[0.98] transition-all flex items-center justify-center gap-2 shadow-sm">
                <span class="material-symbols-outlined">person_add</span>
                Create account
            </button>
        </form>
        <div class="mt-8 text-center pt-6 border-t border-surface-variant">
            <p class="text-body-sm text-secondary">
                Already registered?
                <a href="{{ route('login') }}" class="text-primary font-bold hover:underline">Log in</a>
            </p>
        </div>
    </div>
</x-guest-layout>
