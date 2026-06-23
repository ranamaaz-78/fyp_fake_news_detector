<x-guest-layout>
    <div class="p-8 pb-4 text-center">
        <h1 class="text-headline-xl-mobile md:text-headline-xl text-on-surface mb-2">Reset your password</h1>
        <p class="text-body-md text-secondary">Enter your email and we'll send you a reset link.</p>
    </div>
    <div class="p-8 pt-4">
        <x-auth-session-status class="mb-4" :status="session('status')" />
        <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
            @csrf
            <div class="space-y-2">
                <label for="email" class="text-label-bold text-on-surface-variant block">Email address</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline">mail</span>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="fni-input pl-10" />
                </div>
                <x-input-error :messages="$errors->get('email')" />
            </div>
            <button type="submit" class="w-full py-4 bg-primary-container text-on-primary text-label-bold rounded-lg hover:opacity-90 transition-all flex items-center justify-center gap-2">
                <span class="material-symbols-outlined">send</span>
                Email reset link
            </button>
        </form>
        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="text-body-sm text-primary font-bold hover:underline">Back to login</a>
        </div>
    </div>
</x-guest-layout>
