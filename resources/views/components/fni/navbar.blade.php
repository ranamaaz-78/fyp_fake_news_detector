@props(['active' => null, 'statusBar' => null])

<nav class="fixed top-0 w-full z-50 fni-glass-nav">
    @if($statusBar)
        <div class="h-1 w-full {{ $statusBar }}"></div>
    @endif
    <div class="flex justify-between items-center h-16 px-4 md:px-gutter max-w-container-max mx-auto">
        <div class="flex items-center gap-6 md:gap-8">
            <a href="{{ route('home') }}" class="text-headline-lg font-bold text-primary">FNI</a>
            <div class="hidden md:flex items-center gap-6">
                <a href="{{ route('home') }}"
                   class="text-body-md pb-1 transition-colors {{ $active === 'home' ? 'text-primary font-bold border-b-2 border-primary' : 'text-on-surface-variant hover:text-primary' }}">
                    Home
                </a>
                <a href="{{ route('home') }}#how-it-works"
                   class="text-body-md text-on-surface-variant hover:text-primary transition-colors">
                    How It Works
                </a>
                @auth
                    <a href="{{ route('history.index') }}"
                       class="text-body-md pb-1 transition-colors {{ $active === 'history' ? 'text-primary font-bold border-b-2 border-primary' : 'text-on-surface-variant hover:text-primary' }}">
                        History
                    </a>
                @endauth
            </div>
        </div>
        <div class="flex items-center gap-3 md:gap-4">
            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.overview') }}" class="hidden sm:inline text-body-md text-on-surface-variant hover:text-primary">Admin</a>
                @endif
                <a href="{{ route('profile.edit') }}" class="hidden sm:inline text-body-md text-on-surface-variant hover:text-primary">Profile</a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-body-md text-on-surface-variant hover:text-primary transition-opacity active:scale-95">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-body-md text-on-surface-variant hover:text-primary transition-opacity active:scale-95">Login</a>
                <a href="{{ route('register') }}" class="bg-primary text-on-primary px-4 md:px-6 py-2 rounded-lg text-label-bold hover:opacity-90 active:scale-95 transition-all shadow-sm">Sign-Up</a>
            @endauth
        </div>
    </div>
</nav>
