@php
    $label = $result['label'];
    $config = match($label) {
        'REAL' => [
            'title' => 'REAL',
            'headerBg' => 'bg-status-real-light',
            'headerBorder' => 'border-status-real/10',
            'text' => 'text-status-real',
            'iconBg' => 'bg-status-real',
            'icon' => 'check_circle',
            'gaugeColor' => 'bg-status-real',
            'gaugeTrack' => 'bg-status-real/20',
            'glow' => 'glow-real',
            'summary' => 'This content appears consistent with credible reporting patterns in our training data.',
        ],
        'FAKE' => [
            'title' => 'FAKE',
            'headerBg' => 'bg-status-fake-light',
            'headerBorder' => 'border-status-fake/10',
            'text' => 'text-status-fake',
            'iconBg' => 'bg-status-fake',
            'icon' => 'cancel',
            'gaugeColor' => 'bg-status-fake',
            'gaugeTrack' => 'bg-status-fake/20',
            'glow' => 'glow-fake',
            'summary' => 'This content shows linguistic patterns commonly associated with misinformation in our model.',
        ],
        default => [
            'title' => 'UNCERTAIN',
            'headerBg' => 'bg-status-uncertain-light',
            'headerBorder' => 'border-status-uncertain/10',
            'text' => 'text-status-uncertain',
            'iconBg' => 'bg-status-uncertain',
            'icon' => 'help',
            'gaugeColor' => 'bg-status-uncertain',
            'gaugeTrack' => 'bg-status-uncertain/20',
            'glow' => 'glow-uncertain',
            'summary' => 'The model lacks sufficient confidence to classify this text definitively. Consider additional sources.',
        ],
    };
    $confidence = $result['confidence'];
    $level = $result['confidence_level'] ?? null;
@endphp

<div class="max-w-container-max mx-auto px-4 md:px-gutter">
    <div class="text-center mb-stack-md">
        <h1 class="text-headline-xl mb-stack-sm">Analysis Complete</h1>
        <p class="text-body-lg text-on-surface-variant max-w-card-max mx-auto">
            Our AI has processed the source material using TF-IDF features and trained classification models.
        </p>
    </div>

    <div class="max-w-card-max mx-auto">
        <x-fni.result-card :glow="$config['glow']">
            <div class="{{ $config['headerBg'] }} p-stack-md flex flex-col md:flex-row items-center justify-between gap-4 border-b {{ $config['headerBorder'] }}">
                <div class="flex items-center gap-4">
                    <div class="{{ $config['iconBg'] }} text-on-primary w-16 h-16 rounded-full flex items-center justify-center shadow-md">
                        <span class="material-symbols-outlined text-[40px] material-symbols-filled">{{ $config['icon'] }}</span>
                    </div>
                    <div>
                        <h2 class="text-result-label {{ $config['text'] }}">{{ $config['title'] }}</h2>
                        <div class="flex items-center gap-2 mt-1 flex-wrap">
                            <x-fni.status-badge :label="$label" :level="$level" />
                        </div>
                    </div>
                </div>
                <div class="text-right w-full md:w-auto">
                    <div class="{{ $config['text'] }} font-bold text-headline-lg">{{ number_format($confidence, 1) }}%</div>
                    <div class="w-full md:w-32 h-2 {{ $config['gaugeTrack'] }} rounded-full mt-1 overflow-hidden">
                        <div class="h-full {{ $config['gaugeColor'] }} rounded-full" style="width: {{ min(100, $confidence) }}%"></div>
                    </div>
                </div>
            </div>

            <div class="p-stack-md space-y-stack-md">
                <section>
                    <h3 class="font-label-bold text-on-surface mb-stack-sm flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">description</span>
                        What we analyzed
                    </h3>
                    <div class="bg-surface-container-low p-stack-md rounded-lg border border-outline-variant">
                        <p class="text-body-md text-on-surface-variant italic leading-relaxed line-clamp-6">"{{ $text }}"</p>
                    </div>
                </section>

                <section>
                    <h3 class="font-label-bold text-on-surface mb-2 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">info</span>
                        Summary
                    </h3>
                    <p class="text-body-md text-on-surface-variant">{{ $config['summary'] }}</p>
                </section>

                <x-fni.confidence-gauge :confidence="$confidence" :color="$config['gaugeColor']" :track="$config['gaugeTrack']" />

                <div class="flex flex-wrap gap-4 text-body-sm text-outline pt-2 border-t border-outline-variant">
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-base">psychology</span>
                        Model: {{ $result['model'] ?? 'N/A' }}
                    </span>
                    @if(!empty($result['response_time_ms']))
                        <span class="flex items-center gap-1">
                            <span class="material-symbols-outlined text-base">speed</span>
                            {{ $result['response_time_ms'] }} ms
                        </span>
                    @endif
                </div>

                <div class="flex flex-wrap gap-3 pt-2">
                    <x-fni.button-primary href="{{ route('home') }}" icon="search_check">Check another</x-fni.button-primary>
                    @auth
                        <x-fni.button-secondary href="{{ route('history.index') }}" icon="history">View History</x-fni.button-secondary>
                    @else
                        <x-fni.button-secondary href="{{ route('register') }}" icon="person_add">Save history — Register</x-fni.button-secondary>
                    @endauth
                </div>
            </div>
        </x-fni.result-card>
    </div>
</div>
