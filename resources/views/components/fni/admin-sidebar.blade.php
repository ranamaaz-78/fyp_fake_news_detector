@props(['active' => 'overview'])

<nav class="h-full w-full flex flex-col py-6 px-4 bg-surface-container-low border-r border-outline-variant">
    <div class="mb-8 px-2">
        <h1 class="text-headline-lg font-bold text-primary">FNI Admin</h1>
        <p class="text-body-sm text-on-surface-variant">System Control</p>
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
               class="flex items-center gap-3 py-3 px-4 rounded-lg transition-all {{ $active === $item['key'] ? 'text-primary font-bold bg-secondary-container' : 'text-on-surface-variant hover:bg-surface-variant' }}">
                <span class="material-symbols-outlined">{{ $item['icon'] }}</span>
                <span class="text-body-md">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </div>
    <div class="mt-auto pt-6 border-t border-outline-variant px-2">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-primary-container flex items-center justify-center text-on-primary font-label-bold">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
            </div>
            <div class="min-w-0">
                <p class="font-label-bold truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-on-surface-variant">Superuser Access</p>
            </div>
        </div>
        <a href="{{ route('home') }}" class="mt-4 flex items-center gap-2 text-body-sm text-primary hover:underline">
            <span class="material-symbols-outlined text-base">arrow_back</span>
            Back to site
        </a>
    </div>
</nav>
