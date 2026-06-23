<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'FNI System' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background text-on-background min-h-screen flex flex-col">
    <nav class="fixed top-0 w-full fni-glass-nav z-50">
        <div class="flex justify-between items-center h-16 px-margin-mobile md:px-8 max-w-container-max mx-auto">
            <a href="{{ route('home') }}" class="text-headline-lg font-bold text-primary">FNI System</a>
            <div class="flex items-center gap-4 md:gap-6 text-label-bold">
                @if(request()->routeIs('login', 'register', 'password.*'))
                    @if(request()->routeIs('login'))
                        <a href="{{ route('register') }}" class="text-primary font-bold border-b-2 border-primary pb-0.5">Sign up</a>
                    @else
                        <a href="{{ route('login') }}" class="text-primary font-bold border-b-2 border-primary pb-0.5">Log in</a>
                    @endif
                @endif
            </div>
        </div>
    </nav>

    <main class="flex-grow flex items-center justify-center pt-24 pb-12 px-margin-mobile">
        <div class="w-full max-w-[440px] bg-surface-container-lowest login-card rounded-xl overflow-hidden">
            {{ $slot }}
        </div>
    </main>

    <footer class="py-stack-md mt-auto">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 max-w-container-max mx-auto px-margin-mobile text-body-sm text-secondary">
            <span class="text-label-caps text-primary">FNI SYSTEM</span>
            <p>© {{ date('Y') }} Fake News Identification System.</p>
        </div>
    </footer>
</body>
</html>
