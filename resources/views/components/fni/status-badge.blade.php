@props(['label' => 'REAL', 'level' => null])

@php
    $config = match($label) {
        'REAL' => ['text' => 'text-status-real', 'bg' => 'bg-status-real-light', 'border' => 'border-status-real/20', 'icon' => 'check_circle'],
        'FAKE' => ['text' => 'text-status-fake', 'bg' => 'bg-status-fake-light', 'border' => 'border-status-fake/20', 'icon' => 'cancel'],
        default => ['text' => 'text-status-uncertain', 'bg' => 'bg-status-uncertain-light', 'border' => 'border-status-uncertain/20', 'icon' => 'help'],
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-label-caps uppercase tracking-wider font-medium {$config['bg']} {$config['text']} border {$config['border']}"]) }}>
    <span class="material-symbols-outlined text-base material-symbols-filled">{{ $config['icon'] }}</span>
    {{ $label }}
    @if($level)
        <span class="normal-case tracking-normal opacity-80">· {{ $level }}</span>
    @endif
</span>
