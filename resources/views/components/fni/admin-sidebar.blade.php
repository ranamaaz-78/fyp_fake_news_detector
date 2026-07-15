@props(['active' => 'overview'])

<nav class="h-full w-full flex flex-col py-6 px-4 bg-[#1a2d54] text-white border-r border-white/10" style="font-family: 'Inter', sans-serif;">
    <div class="mb-8 px-2">
        <a href="{{ route('home') }}">
            <img src="{{ asset('assets/images/navwhitelogo.svg') }}" alt="logo" style="max-height: 38px; margin-bottom: 8px;">
        </a>
        <p class="text-xs text-white/60 tracking-wider font-semibold uppercase">System Control</p>
    </div>
    <div class="flex flex-col gap-1 grow">
        @foreach([
            ['route' => 'admin.overview', 'key' => 'overview', 'icon' => 'dashboard', 'label' => 'Overview'],
            ['route' => 'admin.datasets', 'key' => 'datasets', 'icon' => 'database', 'label' => 'Datasets'],
            ['route' => 'admin.training', 'key' => 'training', 'icon' => 'model_training', 'label' => 'Model Training'],
            ['route' => 'admin.users', 'key' => 'users', 'icon' => 'group', 'label' => 'Users'],
            ['route' => 'admin.predictions', 'key' => 'predictions', 'icon' => 'analytics', 'label' => 'Predictions'],
            ['route' => 'admin.audit', 'key' => 'audit', 'icon' => 'receipt_long', 'label' => 'Audit Log'],
        ] as $item)
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 py-3 px-4 rounded-lg transition-all {{ $active === $item['key'] ? 'bg-[#F54329] text-white font-bold shadow-md' : 'text-white/80 hover:text-white hover:bg-white/10' }}">
                <span class="material-symbols-outlined text-[20px]">{{ $item['icon'] }}</span>
                <span class="text-sm font-medium">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </div>
    <div class="mt-auto pt-6 border-t border-white/10 px-2">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-white font-bold" style="font-size: 15px;">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
            </div>
            <div class="min-w-0">
                <p class="text-sm font-semibold truncate text-white">{{ auth()->user()->name }}</p>
                <p class="text-[11px] text-white/50 font-medium">Superuser Access</p>
            </div>
        </div>
        <a href="{{ route('home') }}" class="mt-4 flex items-center gap-2 text-xs font-semibold text-[#F54329] hover:text-[#f86551] transition-all no-underline">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            Back to site
        </a>
    </div>
</nav>
