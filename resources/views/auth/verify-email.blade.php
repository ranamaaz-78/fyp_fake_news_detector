<x-guest-layout>
    <div class="p-8 text-center">
        <div class="w-16 h-16 bg-trust-blue-light rounded-full flex items-center justify-center mx-auto mb-4">
            <span class="material-symbols-outlined text-primary text-3xl material-symbols-filled">mark_email_unread</span>
        </div>
        <h1 class="text-headline-xl-mobile md:text-headline-xl text-on-surface mb-4">Verify your email</h1>
        <p class="text-body-md text-secondary mb-6">
            Thanks for signing up! Before getting started, please verify your email address by clicking the link we sent you.
        </p>
        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 text-body-sm font-medium text-status-real bg-status-real-light rounded-lg p-3">
                A new verification link has been sent to your email address.
            </div>
        @endif
        <div class="flex flex-col gap-3">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="w-full py-3 bg-primary-container text-on-primary text-label-bold rounded-lg hover:opacity-90 transition-all">
                    Resend verification email
                </button>
            </form>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full py-3 text-body-sm text-on-surface-variant hover:text-primary transition-colors">
                    Log out
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
