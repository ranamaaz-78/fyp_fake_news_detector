@props(['active' => 'overview', 'title' => 'Admin'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} | FNI Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background text-on-surface min-h-screen">
    <div class="flex min-h-screen">
        <aside class="hidden lg:block w-64 fixed left-0 top-0 h-screen z-50">
            <x-fni.admin-sidebar :active="$active" />
        </aside>

        <div class="flex-1 lg:ml-64 min-w-0">
            <header class="sticky top-0 z-40 flex justify-between items-center h-16 px-4 md:px-8 bg-surface/80 backdrop-blur-md border-b border-outline-variant shadow-sm">
                <div class="lg:hidden">
                    <span class="text-headline-lg font-bold text-primary">FNI Admin</span>
                </div>
                <div class="hidden md:flex items-center gap-4 flex-1 max-w-md">
                    <span class="material-symbols-outlined text-on-surface-variant">search</span>
                    <span class="text-body-sm text-on-surface-variant">Admin panel</span>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('home') }}" class="text-body-sm text-primary hover:underline">View site</a>
                </div>
            </header>

            @if (session('status'))
                <div class="mx-4 md:mx-8 mt-4 rounded-lg bg-status-real-light border border-status-real/20 px-4 py-3 text-body-sm text-status-real">
                    {{ session('status') }}
                </div>
            @endif

            <main class="p-4 md:p-8 max-w-6xl">
                {{ $slot }}
            </main>
        </div>
    </div>

    {{-- Mobile nav --}}
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-surface-container-lowest border-t border-outline-variant flex justify-around py-2 z-50 text-xs">
        <a href="{{ route('admin.overview') }}" class="flex flex-col items-center p-2 {{ $active === 'overview' ? 'text-primary' : 'text-on-surface-variant' }}">
            <span class="material-symbols-outlined">dashboard</span>
            Overview
        </a>
        <a href="{{ route('admin.users') }}" class="flex flex-col items-center p-2 {{ $active === 'users' ? 'text-primary' : 'text-on-surface-variant' }}">
            <span class="material-symbols-outlined">group</span>
            Users
        </a>
        <a href="{{ route('admin.predictions') }}" class="flex flex-col items-center p-2 {{ $active === 'predictions' ? 'text-primary' : 'text-on-surface-variant' }}">
            <span class="material-symbols-outlined">analytics</span>
            Logs
        </a>
        <a href="{{ route('admin.audit') }}" class="flex flex-col items-center p-2 {{ $active === 'audit' ? 'text-primary' : 'text-on-surface-variant' }}">
            <span class="material-symbols-outlined">receipt_long</span>
            Audit
        </a>
    </nav>

    @stack('scripts')
</body>
</html>
