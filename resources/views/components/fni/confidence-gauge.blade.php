@props(['confidence' => 0, 'color' => 'bg-status-real', 'track' => 'bg-status-real/20'])

<div>
    <div class="flex justify-between text-body-sm mb-2">
        <span class="font-label-bold text-on-surface">Confidence</span>
        <span class="font-bold">{{ number_format($confidence, 1) }}%</span>
    </div>
    <div class="w-full h-2 {{ $track }} rounded-full overflow-hidden">
        <div class="{{ $color }} h-full rounded-full transition-all duration-500" style="width: {{ min(100, $confidence) }}%"></div>
    </div>
</div>
