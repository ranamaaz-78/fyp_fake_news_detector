@extends('layouts.fni')

@section('title', 'FNI - Fake News Identifier')

@php $navActive = 'home'; @endphp

@section('mainClass', 'pt-32 pb-stack-lg overflow-x-hidden')

@section('content')
<section class="max-w-container-max mx-auto px-4 md:px-gutter text-center">
    <div class="flex flex-col items-center gap-stack-sm mb-stack-md">
        <div class="inline-flex items-center gap-2 bg-trust-blue-light text-trust-blue-dark px-3 py-1 rounded-full text-label-caps border border-primary-fixed">
            <span class="material-symbols-outlined text-base material-symbols-filled">verified</span>
            AI-POWERED VERIFICATION ENGINE
        </div>
        <h1 class="text-headline-xl md:text-headline-xl text-on-surface max-w-2xl">
            Verify any news in seconds.
        </h1>
        <p class="text-body-lg text-on-surface-variant max-w-3xl">
            Paste a headline, full article text, a URL, or an image — our AI tells you if it's real, fake, or uncertain.
        </p>
    </div>

    <div class="flex flex-wrap justify-center gap-4 md:gap-6 mb-stack-lg">
        <div class="flex items-center gap-2 text-on-surface-variant text-label-bold bg-surface-container-low px-4 py-2 rounded-lg">
            <span class="material-symbols-outlined text-status-real">check_circle</span>
            85%+ accuracy
        </div>
        <div class="flex items-center gap-2 text-on-surface-variant text-label-bold bg-surface-container-low px-4 py-2 rounded-lg">
            <span class="material-symbols-outlined text-primary">bolt</span>
            &lt; 3 seconds
        </div>
        <div class="flex items-center gap-2 text-on-surface-variant text-label-bold bg-surface-container-low px-4 py-2 rounded-lg">
            <span class="material-symbols-outlined text-secondary">code</span>
            Free &amp; open-source
        </div>
    </div>

    <div class="max-w-card-max mx-auto text-left" x-data="{ activeTab: '{{ $errors->has('image') ? 'image' : (old('url') ? 'url' : 'text') }}' }">
        <div class="bg-surface-container-lowest rounded-xl shadow-fni border border-outline-variant overflow-hidden">
            <div class="flex border-b border-outline-variant">
                <button
                    type="button"
                    @click="activeTab = 'text'"
                    :class="activeTab === 'text' ? 'text-primary active-tab-border' : 'text-on-surface-variant hover:text-primary'"
                    class="flex-1 py-4 text-label-bold transition-all flex items-center justify-center gap-2"
                >
                    <span class="material-symbols-outlined">description</span>
                    Paste Text
                </button>
                <button
                    type="button"
                    @click="activeTab = 'url'"
                    :class="activeTab === 'url' ? 'text-primary active-tab-border' : 'text-on-surface-variant hover:text-primary'"
                    class="flex-1 py-4 text-label-bold transition-all flex items-center justify-center gap-2"
                >
                    <span class="material-symbols-outlined">link</span>
                    From URL
                </button>
                <button
                    type="button"
                    @click="activeTab = 'image'"
                    :class="activeTab === 'image' ? 'text-primary active-tab-border' : 'text-on-surface-variant hover:text-primary'"
                    class="flex-1 py-4 text-label-bold transition-all flex items-center justify-center gap-2"
                >
                    <span class="material-symbols-outlined">image</span>
                    From Image
                </button>
            </div>
            <form method="POST" action="{{ route('news.check') }}" class="p-6" enctype="multipart/form-data">
                @csrf
                <div x-show="activeTab === 'text'" x-cloak>
                    <x-fni.input-textarea
                        name="text"
                        :value="$prefill"
                        :min="config('fni.min_input_chars')"
                        :max="config('fni.max_input_chars')"
                        x-bind:required="activeTab === 'text'"
                        x-bind:disabled="activeTab !== 'text'"
                    />
                </div>
                <div x-show="activeTab === 'url'" x-cloak>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none">language</span>
                        <input
                            type="url"
                            name="url"
                            value="{{ old('url') }}"
                            placeholder="https://news-site.com/article-slug"
                            x-bind:required="activeTab === 'url'"
                            x-bind:disabled="activeTab !== 'url'"
                            class="fni-input w-full pl-12"
                        />
                    </div>
                    @error('url')
                        <p class="mt-2 text-body-sm text-error">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-label-caps uppercase tracking-wide text-on-surface-variant">We fetch and extract article text from the page</p>
                </div>
                <div x-show="activeTab === 'image'" x-cloak x-data="{ fileName: '' }">
                    <label
                        class="flex flex-col items-center justify-center gap-3 w-full py-10 px-6 border-2 border-dashed border-outline-variant rounded-xl cursor-pointer hover:border-primary hover:bg-surface-container-low transition-colors text-center"
                    >
                        <span class="material-symbols-outlined text-primary text-5xl">add_photo_alternate</span>
                        <span class="text-body-md text-on-surface" x-show="!fileName">Click to upload a screenshot or photo of the news</span>
                        <span class="text-body-md text-primary font-medium break-all" x-show="fileName" x-text="fileName"></span>
                        <span class="text-label-caps uppercase tracking-wide text-on-surface-variant">
                            JPG, PNG or WEBP &middot; up to {{ (int) (config('fni.image_max_kb') / 1024) }} MB
                        </span>
                        <input
                            type="file"
                            name="image"
                            accept="image/*"
                            class="hidden"
                            x-bind:required="activeTab === 'image'"
                            x-bind:disabled="activeTab !== 'image'"
                            @change="fileName = $event.target.files.length ? $event.target.files[0].name : ''"
                        />
                    </label>
                    @error('image')
                        <p class="mt-2 text-body-sm text-error">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-label-caps uppercase tracking-wide text-on-surface-variant">We read the text from your image and analyse it</p>
                </div>
                <div class="mt-6 flex flex-col items-center gap-4">
                    <x-fni.button-primary type="submit" size="lg" icon="search_check" class="group">
                        Check Credibility
                    </x-fni.button-primary>
                    <p class="text-body-sm text-on-surface-variant text-center opacity-70">
                        Results are generated by AI analysis and should be used for informational purposes.
                    </p>
                </div>
            </form>
        </div>
    </div>
</section>

<section class="max-w-container-max mx-auto px-4 md:px-gutter mt-stack-lg pt-stack-lg" id="how-it-works">
    <div class="text-center mb-stack-md">
        <h2 class="text-headline-xl text-on-surface mb-2">How It Works</h2>
        <div class="h-1 w-20 bg-primary mx-auto rounded-full"></div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="bg-surface-container-low p-8 rounded-xl border border-outline-variant flex flex-col items-center text-center group hover:shadow-fni-lg transition-shadow">
            <div class="w-16 h-16 bg-primary-fixed rounded-full flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                <span class="material-symbols-outlined text-primary text-4xl material-symbols-filled">input</span>
            </div>
            <h3 class="text-headline-lg mb-4">1. Input News</h3>
            <p class="text-body-md text-on-surface-variant">Provide a headline, a full story, a URL, or an image. Our system processes text using NLP preprocessing.</p>
        </div>
        <div class="bg-surface-container-low p-8 rounded-xl border border-outline-variant flex flex-col items-center text-center group hover:shadow-fni-lg transition-shadow">
            <div class="w-16 h-16 bg-secondary-fixed rounded-full flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                <span class="material-symbols-outlined text-secondary text-4xl material-symbols-filled">psychology</span>
            </div>
            <h3 class="text-headline-lg mb-4">2. AI Analysis</h3>
            <p class="text-body-md text-on-surface-variant">TF-IDF vectorization and ML models detect misinformation linguistic patterns.</p>
        </div>
        <div class="bg-surface-container-low p-8 rounded-xl border border-outline-variant flex flex-col items-center text-center group hover:shadow-fni-lg transition-shadow">
            <div class="w-16 h-16 bg-status-real-light rounded-full flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                <span class="material-symbols-outlined text-status-real text-4xl material-symbols-filled">fact_check</span>
            </div>
            <h3 class="text-headline-lg mb-4">3. Instant Verdict</h3>
            <p class="text-body-md text-on-surface-variant">Receive Real, Fake, or Uncertain with a confidence percentage.</p>
        </div>
    </div>
</section>

<div class="fixed top-0 left-0 w-full h-full -z-10 pointer-events-none opacity-20">
    <div class="absolute top-[20%] left-[10%] w-96 h-96 bg-primary-fixed blur-[120px] rounded-full"></div>
    <div class="absolute bottom-[20%] right-[10%] w-[500px] h-[500px] bg-secondary-fixed blur-[150px] rounded-full"></div>
</div>
@endsection
