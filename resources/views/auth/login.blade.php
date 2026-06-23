<x-guest-layout>
    <div class="p-8 pb-4 text-center">
        <h1 class="text-headline-xl-mobile md:text-headline-xl text-on-background mb-2">Welcome back</h1>
        <p class="text-body-md text-secondary">Log in to view your prediction history.</p>
    </div>
    <div class="p-8 pt-4">
        <x-auth-session-status class="mb-4" :status="session('status')" />
        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf
            <div class="space-y-2">
                <label for="email" class="text-label-bold text-on-surface-variant block">Email address</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline">mail</span>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                           class="fni-input pl-10" placeholder="you@example.com" />
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>
            <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <label for="password" class="text-label-bold text-on-surface-variant">Password</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-label-bold text-primary hover:underline">Forgot password?</a>
                    @endif
                </div>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline">lock</span>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                           class="fni-input pl-10" placeholder="••••••••" />
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>
            <label class="flex items-center gap-2 text-body-sm text-on-surface-variant">
                <input type="checkbox" name="remember" class="rounded border-outline-variant text-primary focus:ring-primary">
                Remember me
            </label>
            <button type="submit" class="w-full py-4 bg-primary-container text-on-primary text-label-bold rounded-lg hover:opacity-90 active:scale-[0.98] transition-all flex items-center justify-center gap-2 shadow-sm">
                <span class="material-symbols-outlined material-symbols-filled">login</span>
                Log in
            </button>
        </form>
        <div class="mt-8 text-center pt-6 border-t border-surface-variant">
            <p class="text-body-sm text-secondary">
                Don't have an account?
                <a href="{{ route('register') }}" class="text-primary font-bold hover:underline">Sign up</a>
            </p>
        </div>
    </div>
</x-guest-layout>
