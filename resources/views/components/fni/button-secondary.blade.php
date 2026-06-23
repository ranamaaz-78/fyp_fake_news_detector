@props(['href' => null, 'icon' => null])

@php
    $classes = 'fni-btn-secondary';
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)<span class="material-symbols-outlined">{{ $icon }}</span>@endif
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => 'submit', 'class' => $classes]) }}>
        @if($icon)<span class="material-symbols-outlined">{{ $icon }}</span>@endif
        {{ $slot }}
    </button>
@endif
