@props(['title' => null, 'value' => null, 'accent' => null, 'icon' => null, 'valueClass' => ''])

<div {{ $attributes->merge(['class' => 'bg-surface-container-lowest p-stack-md rounded-xl shadow-fni border border-outline-variant/20' . ($accent ? " border-t-4 {$accent}" : '')]) }}>
    @if($title)
        <p class="text-on-surface-variant font-label-caps uppercase tracking-wider">{{ $title }}</p>
    @endif
    <div class="flex items-end justify-between mt-1">
        <p class="text-headline-lg font-bold {{ $valueClass ?: 'text-on-surface' }}">{{ $value }}</p>
        @if($icon)
            <span class="material-symbols-outlined text-on-surface-variant">{{ $icon }}</span>
        @endif
    </div>
    {{ $slot }}
</div>
