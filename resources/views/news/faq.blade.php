@extends('layouts.frontend')

@section('title', 'Faq - VeriFact AI')

@section('bodyClass', 'innerpage')

@section('content')
<!-- rts faq area start -->
    <section class="rts-faq-area inner-one rts-section-gap2" data-bg-src="{{ asset(\'assets/images/banner/10.webp\') }}">
        <div class="container">
            <div class="section-inner">
                <div class="section-title-area center-style">
                    <h2 class="h3 section-title rts-text-anime-style-1 text-transform-0">Frequently Asked Questions <br> <span>About VeriFact AI</span>
                    </h2>
                </div>
                <div class="rts-accordion accordion-flush wow fadeInUp" data-wow-delay=".2s" id="rts-accordion">
                    <div class="accordion-item active">
                        <div class="accordion-header" id="first">
                            <h3 class="accordion-button collapse show" data-bs-toggle="collapse" data-bs-target="#item__one" aria-expanded="false" aria-controls="item__one">
                                How does the heuristic text credibility checker work?
                            </h3>
                        </div>
                        <div id="item__one" class="accordion-collapse collapse collapse show" data-bs-parent="#rts-accordion">
                            <div class="accordion-body">
                                <p class="desc">VeriFact AI analyzes text syntax, exclamation patterns, clickbait indicators, sentiment intensity, and checks for known conspiracy vocabulary/patterns locally in your browser.</p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <div class="accordion-header" id="two">
                            <h3 class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#item__two" aria-expanded="false" aria-controls="item__two">
                                Is my personal data or pasted text stored on any servers?
                            </h3>
                        </div>
                        <div id="item__two" class="accordion-collapse collapse" data-bs-parent="#rts-accordion">
                            <div class="accordion-body">
                                <p class="desc">No, VeriFact AI runs the core heuristics checker fully on your local browser. Your claims and pasted articles are never uploaded to any remote databases or servers, ensuring complete privacy.</p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <div class="accordion-header" id="three">
                            <h3 class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#item__three" aria-expanded="false" aria-controls="item__three">
                                What do the different credibility verdicts mean?
                            </h3>
                        </div>
                        <div id="item__three" class="accordion-collapse collapse" data-bs-parent="#rts-accordion">
                            <div class="accordion-body">
                                <p class="desc">We offer three main ranges: Highly Credible (80%+ score, neutral tone, standard syntax), Suspicious (50-79% score, slightly sensationalized or bias-leaning), and Low Credibility (below 50%, high exclamation density, clickbait cues, or conspiracy phrases).</p>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <div class="accordion-header" id="four">
                            <h3 class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#item__four" aria-expanded="false" aria-controls="item__four">
                                How can I integrate the VeriFact checker into my newsroom?
                            </h3>
                        </div>
                        <div id="item__four" class="accordion-collapse collapse" data-bs-parent="#rts-accordion">
                            <div class="accordion-body">
                                <p class="desc">You can integrate our engine using our custom plugins or the Unified Newsroom API. Head over to our Integrations page or contact our support team for full technical documentation.</p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <div class="accordion-header" id="five">
                            <h3 class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#item__five" aria-expanded="false" aria-controls="item__five">
                                Does it detect deepfakes or edited images?
                            </h3>
                        </div>
                        <div id="item__five" class="accordion-collapse collapse" data-bs-parent="#rts-accordion">
                            <div class="accordion-body">
                                <p class="desc">Currently, our client-side toolkit focuses on textual claim heuristics, NLP sentiment analysis, and stylistic indicators. Image and media verification features are currently in active development.</p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <div class="accordion-header" id="six">
                            <h3 class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#item__six" aria-expanded="false" aria-controls="item__six">
                                Are the ratings 100% accurate?
                            </h3>
                        </div>
                        <div id="item__six" class="accordion-collapse collapse" data-bs-parent="#rts-accordion">
                            <div class="accordion-body">
                                <p class="desc">Our heuristics analyze style, sentiment, and patterns of conspiracy theories. While highly accurate at detecting stylistic disinformation, clickbait, and extreme bias, we recommend using VeriFact AI as an auxiliary diagnostic tool alongside manual source verification.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- rts faq area end -->

    <!-- rts cta area start -->
    <section class="rts-cta-area area-2 pt--100">
        <div class="container">
            <div class="section-inner">
                <div class="section-title-area">
                    <h2 class="h3 section-title cw rts-text-anime-style-1 text-transform-0">Start Detecting Rumors <br> <span class="cw">Smarter With VeriFact AI</span></h2>
                </div>
                <p class="desc wow fadeInUp" data-wow-delay=".2s">Take control of your information hygiene with an AI tool built to verify claims instantly and keep you safe from online disinformation campaigns.</p>
                <div class="button-area wow fadeInUp" data-wow-delay=".4s">
                    <a href="{{ route('home') }}#verifier" class="rts-btn btn-primary">
                        Verify Claims Now
                        <span class="icon">
                            <svg width="19" height="8" viewBox="0 0 19 8" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M17.9 0.900391L0.900024 0.900391" stroke="#1E3A8A" stroke-width="1.8"
                                    stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M17.9031 0.900781L11.8531 6.92578" stroke="#1E3A8A" stroke-width="1.8"
                                    stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </span>
                    </a>
                </div>
                <div class="shape-area">
                    <img src="{{ asset(\'assets/images/cta/cta-bg-shape-04.svg\') }}" alt="">
                    <img src="{{ asset(\'assets/images/cta/cta-bg-shape-05.svg\') }}" alt="">
                </div>
            </div>
        </div>
    </section>
    <!-- rts cta area end -->
@endsection
