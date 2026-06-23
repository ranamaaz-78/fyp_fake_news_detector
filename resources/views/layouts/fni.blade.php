<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'FNI - Fake News Identifier')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-background text-on-surface selection:bg-primary-fixed">
    @php
        $navActive = match (true) {
            request()->routeIs('home') => 'home',
            request()->routeIs('history.*') => 'history',
            default => null,
        };
        $statusBar = $statusBar ?? match ($result['label'] ?? null) {
            'REAL' => 'bg-status-real',
            'FAKE' => 'bg-status-fake',
            'UNCERTAIN' => 'bg-status-uncertain',
            default => null,
        };
    @endphp

    <x-fni.navbar :active="$navActive" :statusBar="$statusBar" />

    @if (session('status'))
        <div class="fixed top-20 left-1/2 -translate-x-1/2 z-50 max-w-md w-full mx-4 rounded-lg bg-status-real-light border border-status-real/20 px-4 py-3 text-body-sm text-status-real shadow-fni animate-reveal">
            {{ session('status') }}
        </div>
    @endif

    <main class="@yield('mainClass', 'pt-24 pb-stack-lg')">
        @yield('content')
    </main>

    <footer class="w-full bg-surface-container-low border-t border-outline-variant">
        <div class="flex flex-col md:flex-row justify-between items-center py-stack-md px-4 md:px-gutter max-w-container-max mx-auto gap-4">
            <div class="flex items-center gap-2 text-center md:text-left flex-wrap justify-center">
                <span class="font-label-bold text-primary">FNI</span>
                <span class="text-body-sm text-on-surface-variant">© {{ date('Y') }} FNI. AI analysis for informational purposes only.</span>
            </div>
        </div>
    </footer>
</body>
</html>
