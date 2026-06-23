@props(['glow' => ''])

<div {{ $attributes->merge(['class' => "fni-card overflow-hidden result-reveal-animation {$glow}"]) }}>
    {{ $slot }}
</div>
