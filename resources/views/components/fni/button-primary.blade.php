@props(['type' => 'submit', 'icon' => null, 'size' => 'default', 'href' => null])

@php
    $classes = $size === 'lg' ? 'fni-btn-primary-lg' : 'fni-btn-primary';
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)<span class="material-symbols-outlined">{{ $icon }}</span>@endif
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => $type, 'class' => $classes]) }}>
        @if($icon)<span class="material-symbols-outlined">{{ $icon }}</span>@endif
        {{ $slot }}
    </button>
@endif
