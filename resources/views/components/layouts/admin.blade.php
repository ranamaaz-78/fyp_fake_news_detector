@props(['active' => 'overview', 'title' => 'Admin'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} | FNI Admin</title>
    
    <!-- Custom Font css -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/custom-font.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        /* Admin custom overrides for template alignment */
        body {
            font-family: 'Inter', 'Outfit', sans-serif !important;
            background-color: #f8fafc; /* Slate 50 background */
        }
        
        .fni-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05), 0 1px 2px rgba(0, 0, 0, 0.02);
            transition: all 0.2s ease;
        }
        
        .fni-card:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        }
        
        /* Stats cards alignment */
        .stat-card-title {
            color: #64748b;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        /* Theme buttons */
        .btn-theme-primary {
            background-color: #F54329 !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            border-radius: 8px !important;
            padding: 10px 20px !important;
            transition: all 0.2s ease !important;
            border: none !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 8px !important;
        }
        
        .btn-theme-primary:hover {
            background-color: #f75d47 !important;
            transform: translateY(-1px);
        }

        .btn-theme-secondary {
            background-color: #1a2d54 !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            border-radius: 8px !important;
            padding: 10px 20px !important;
            transition: all 0.2s ease !important;
            border: none !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 8px !important;
        }

        .btn-theme-secondary:hover {
            background-color: #243e75 !important;
            transform: translateY(-1px);
        }
        
        /* Custom tables */
        table thead {
            background-color: #f1f5f9 !important;
            border-bottom: 2px solid #e2e8f0;
        }
        
        table th {
            color: #475569 !important;
            font-weight: 700 !important;
            font-size: 12px !important;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
    </style>
</head>
<body class="bg-[#f8fafc] text-slate-800 min-h-screen">
    <div class="flex min-h-screen">
        <aside class="hidden lg:block w-64 fixed left-0 top-0 h-screen z-50">
            <x-fni.admin-sidebar :active="$active" />
        </aside>

        <div class="flex-1 lg:ml-64 min-w-0">
            <header class="sticky top-0 z-40 flex justify-between items-center h-16 px-4 md:px-8 bg-white/80 backdrop-blur-md border-b border-slate-200 shadow-sm">
                <div class="lg:hidden">
                    <img src="{{ asset('assets/images/navcolorlogo.svg') }}" alt="logo" style="max-height: 35px;">
                </div>
                <div class="hidden md:flex items-center gap-4 flex-1 max-w-md">
                    <span class="material-symbols-outlined text-slate-400">search</span>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">VeriFact AI Admin Panel</span>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('home') }}" class="btn btn-outline-primary btn-sm rounded-pill font-semibold px-3 text-xs d-flex align-items-center gap-1" style="border-color: #1a2d54; color: #1a2d54;">
                        <span class="material-symbols-outlined text-sm">home</span>
                        View Site
                    </a>
                </div>
            </header>

            @if (session('status'))
                <div class="mx-4 md:mx-8 mt-4 alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 8px;">
                    {{ session('status') }}
                </div>
            @endif

            <main class="p-4 md:p-8 max-w-6xl">
                {{ $slot }}
            </main>
        </div>
    </div>

    {{-- Mobile nav --}}
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-[#1a2d54] border-t border-white/10 flex justify-around py-2 z-50 text-[10px] text-white/70">
        <a href="{{ route('admin.overview') }}" class="flex flex-col items-center p-1 no-underline {{ $active === 'overview' ? 'text-white font-bold' : 'text-white/60' }}">
            <span class="material-symbols-outlined text-[20px]">dashboard</span>
            Overview
        </a>
        <a href="{{ route('admin.datasets') }}" class="flex flex-col items-center p-1 no-underline {{ $active === 'datasets' ? 'text-white font-bold' : 'text-white/60' }}">
            <span class="material-symbols-outlined text-[20px]">database</span>
            Datasets
        </a>
        <a href="{{ route('admin.training') }}" class="flex flex-col items-center p-1 no-underline {{ $active === 'training' ? 'text-white font-bold' : 'text-white/60' }}">
            <span class="material-symbols-outlined text-[20px]">model_training</span>
            Training
        </a>
        <a href="{{ route('admin.users') }}" class="flex flex-col items-center p-1 no-underline {{ $active === 'users' ? 'text-white font-bold' : 'text-white/60' }}">
            <span class="material-symbols-outlined text-[20px]">group</span>
            Users
        </a>
        <a href="{{ route('admin.predictions') }}" class="flex flex-col items-center p-1 no-underline {{ $active === 'predictions' ? 'text-white font-bold' : 'text-white/60' }}">
            <span class="material-symbols-outlined text-[20px]">analytics</span>
            Logs
        </a>
        <a href="{{ route('admin.audit') }}" class="flex flex-col items-center p-1 no-underline {{ $active === 'audit' ? 'text-white font-bold' : 'text-white/60' }}">
            <span class="material-symbols-outlined text-[20px]">receipt_long</span>
            Audit
        </a>
    </nav>

    @stack('scripts')
</body>
</html>
