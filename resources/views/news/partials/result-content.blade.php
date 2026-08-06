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

    $explanation = $result['explanation'] ?? [];
    $factCheck = $result['fact_check'] ?? [];
    $evidence = $factCheck['evidence'] ?? [];
    $signals = $result['signals'] ?? [];
    $verdictSource = $result['verdict_source'] ?? 'model';
    $viaFactCheck = $verdictSource === 'fact_check';
    $verdictSourceLabel = [
        'fact_check' => 'fact check',
        'content_signal' => 'content check',
        'model' => 'style model',
    ][$verdictSource] ?? 'style model';

    $summary = $explanation['plain'] ?? $config['summary'];
    $headline = $explanation['headline'] ?? 'What we found';
    $disclaimer = $explanation['disclaimer']
        ?? 'This tool checks writing style and a limited database of known facts. It cannot verify every real-world claim. Always confirm important news with a trusted source.';

    $sourceNames = [
        'local_kb' => 'VeriFact fact database',
        'wikidata' => 'Wikidata',
        'google_factcheck' => 'Google Fact Check',
    ];

    $toneStyles = [
        'negative' => ['border-status-fake/30 bg-status-fake-light', 'text-status-fake', 'warning'],
        'positive' => ['border-status-real/30 bg-status-real-light', 'text-status-real', 'check_circle'],
        'neutral' => ['border-outline-variant bg-surface-container-low', 'text-outline', 'info'],
    ];
@endphp

<div class="max-w-container-max mx-auto px-4 md:px-gutter">
    <div class="text-center mb-stack-md">
        <h1 class="text-headline-xl mb-stack-sm">Analysis Complete</h1>
        <p class="text-body-lg text-on-surface-variant max-w-card-max mx-auto">
            Here is what we found, and how we worked it out.
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
                        {{ $headline }}
                    </h3>
                    <p class="text-body-md text-on-surface-variant">{{ $summary }}</p>
                </section>

                @if (!empty($evidence))
                    <section>
                        <h3 class="font-label-bold text-on-surface mb-stack-sm flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">balance</span>
                            What we checked against real records
                        </h3>
                        <ul class="space-y-3">
                            @foreach ($evidence as $item)
                                @php
                                    $isContradiction = ($item['verdict'] ?? '') === 'CONTRADICTED';
                                    $url = $item['url'] ?? null;
                                    $isSafeUrl = $url && preg_match('#^https?://#i', $url);
                                @endphp
                                <li class="flex items-start gap-3 p-stack-sm rounded-lg border {{ $isContradiction ? 'border-status-fake/30 bg-status-fake-light' : 'border-status-real/30 bg-status-real-light' }}">
                                    <span class="material-symbols-outlined {{ $isContradiction ? 'text-status-fake' : 'text-status-real' }}">
                                        {{ $isContradiction ? 'cancel' : 'check_circle' }}
                                    </span>
                                    <div class="min-w-0">
                                        <p class="text-body-md text-on-surface">{{ $item['statement'] ?? '' }}</p>
                                        <div class="flex flex-wrap items-center gap-3 mt-1">
                                            <span class="text-body-sm text-outline">
                                                {{ $sourceNames[$item['source'] ?? ''] ?? ($item['source'] ?? 'Source') }}
                                            </span>
                                            @if ($isSafeUrl)
                                                <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                                                   class="text-body-sm text-primary font-bold hover:underline">
                                                    View source
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>

                        @if ($viaFactCheck && $label === 'FAKE' && ($result['style_label'] ?? null) === 'REAL')
                            <p class="text-body-sm text-on-surface-variant mt-stack-sm p-stack-sm rounded-lg bg-surface-container-low border border-outline-variant">
                                The writing style alone looked genuine, but a fact we could check does not match.
                                A false statement can still be written calmly, so the fact check decides this result.
                            </p>
                        @endif
                        @if (!empty($factCheck['degraded']))
                            <p class="text-body-sm text-on-surface-variant mt-stack-sm p-stack-sm rounded-lg bg-surface-container-low border border-outline-variant">
                                Some online fact sources could not be reached, so only our offline database was used.
                            </p>
                        @endif
                    </section>
                @endif

                @if (!empty($signals))
                    <section>
                        <h3 class="font-label-bold text-on-surface mb-stack-sm flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">query_stats</span>
                            How we reached this result
                        </h3>
                        <ul class="space-y-2">
                            @foreach ($signals as $signal)
                                @php
                                    [$box, $iconColor, $icon] = $toneStyles[$signal['tone'] ?? 'neutral'] ?? $toneStyles['neutral'];
                                @endphp
                                <li class="flex items-start gap-3 p-stack-sm rounded-lg border {{ $box }}">
                                    <span class="material-symbols-outlined text-base {{ $iconColor }}">{{ $icon }}</span>
                                    <p class="text-body-sm text-on-surface-variant min-w-0">
                                        <span class="font-label-bold text-on-surface">{{ $signal['label'] ?? '' }}</span>
                                        &mdash; {{ $signal['detail'] ?? '' }}
                                    </p>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif

                <x-fni.confidence-gauge :confidence="$confidence" :color="$config['gaugeColor']" :track="$config['gaugeTrack']" />

                <p class="text-body-sm text-on-surface-variant p-stack-sm rounded-lg bg-status-uncertain-light border-l-4 border-status-uncertain">
                    {{ $disclaimer }}
                </p>

                <div class="flex flex-wrap gap-4 text-body-sm text-outline pt-2 border-t border-outline-variant">
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-base">psychology</span>
                        Model: {{ $result['model'] ?? 'N/A' }}
                    </span>
                    @if (isset($result['verdict_source']))
                        <span class="flex items-center gap-1">
                            <span class="material-symbols-outlined text-base">rule</span>
                            Decided by: {{ $verdictSourceLabel }}
                        </span>
                    @endif
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
