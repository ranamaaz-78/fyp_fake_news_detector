@extends('layouts.frontend')

@section('title', 'How It Works - VeriFact AI')

@section('bodyClass', 'innerpage')

@section('content')
<!-- rts features area start -->
    <section class="rts-feature-area inner-one rts-section-gap2" data-bg-src="{{ asset(\'assets/images/banner/10.webp\') }}">
        <div class="container-1320">
            <div class="section-title-area center-style">
                <p class="sub-title"><span class="dot"></span>WORKFLOW</p>
                <h2 class="h3 section-title rts-text-anime-style-1 text-transform-0">How VeriFact AI <br> <span> Checks News Claims.</span></h2>
                <p class="desc wow fadeInUp" data-wow-delay=".4s">Understand the step-by-step process our client-side heuristic engine uses to determine truth and expose falsehoods.</p>
            </div>
        </div>
    </section>
    <!-- rts features area end -->

    <!-- rts features area start -->
    <section class="rts-features-area rts-section-gap2">
        <div class="container-1320">
            <div class="section-title-area">
                <div class="left">
                    <p class="sub-title"><img src="{{ asset(\'assets/images/feature/icon/08.svg\') }}" alt="">Workflow</p>
                    <h2 class="h3 section-title rts-text-anime-style-1 text-transform-0">Step-by-Step Verification <br> <span>Pipeline Workflow</span></h2>
                </div>
                <div class="right">
                    <a href="{{ route('home') }}#verifier" class="rts-btn btn-primary wow fadeInUp" data-wow-delay=".2s">
                        Try Verifier Widget
                        <span class="icon">
                            <svg width="19" height="8" viewBox="0 0 19 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17.9 0.900391L0.900024 0.900391" stroke="#1E3A8A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M17.9031 0.900781L11.8531 6.92578" stroke="#1E3A8A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </span>
                    </a>
                </div>
            </div>
            <div class="section-inner mt--60">
                <div class="row g-28 gy-48">
                    <div class="col-lg-4 col-md-6">
                        <div class="single-features wow fadeInRight" data-wow-delay=".2s">
                            <div class="image-area">
                                <img src="{{ asset(\'assets/images/service/19.webp\') }}" alt="features">
                            </div>
                            <div class="content-area">
                                <h3 class="title">1. Input Text Claim</h3>
                                <p class="desc">You enter or paste any suspicious news headline, article body, or claim text into the analyzer widget.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="single-features wow fadeInRight" data-wow-delay=".4s">
                            <div class="image-area">
                                <img src="{{ asset(\'assets/images/service/02.webp\') }}" alt="features">
                            </div>
                            <div class="content-area">
                                <h3 class="title">2. Heuristic Feature Extraction</h3>
                                <p class="desc">Our NLP engine extracts spelling discrepancies, capital shouts, excessive punctuation, and clickbait phrases.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="single-features wow fadeInRight" data-wow-delay=".6s">
                            <div class="image-area">
                                <img src="{{ asset(\'assets/images/service/03.webp\') }}" alt="features">
                            </div>
                            <div class="content-area">
                                <h3 class="title">3. Conspiracy & Sentiment Check</h3>
                                <p class="desc">We cross-reference the text for conspiracy keyphrases and score the emotional density of the write-up.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="single-features wow fadeInRight" data-wow-delay=".2s">
                            <div class="image-area">
                                <img src="{{ asset(\'assets/images/service/04.webp\') }}" alt="features">
                            </div>
                            <div class="content-area">
                                <h3 class="title">4. Database Lookup</h3>
                                <p class="desc">Our simulator runs database matches against active hoaxes and verifies domain source safety indices.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="single-features wow fadeInRight" data-wow-delay=".4s">
                            <div class="image-area">
                                <img src="{{ asset(\'assets/images/service/17.webp\') }}" alt="features">
                            </div>
                            <div class="content-area">
                                <h3 class="title">5. Verdict Generation</h3>
                                <p class="desc">We calculate a custom Trust Score and display the verdict (Credible, Mixed, or Suspicious).</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="single-features wow fadeInRight" data-wow-delay=".6s">
                            <div class="image-area">
                                <img src="{{ asset(\'assets/images/service/18.webp\') }}" alt="features">
                            </div>
                            <div class="content-area">
                                <h3 class="title">6. Detailed Sub-Metrics</h3>
                                <p class="desc">You get detailed diagnostic scores on style, source registry matches, bias alerts, and domain registry authority.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- rts features area end -->

    <!-- rts faq area start -->
    <section class="rts-faq-area area-2 rts-section-gap2" data-bg-src="{{ asset(\'assets/images/faq/faq-bg.webp\') }}">
        <div class="container">
            <div class="section-inner">
                <div class="section-title-area">
                    <div class="left">
                        <p class="sub-title"><img src="{{ asset(\'assets/images/feature/icon/11.svg\') }}" alt="">FAQ</p>
                        <h2 class="h3 section-title rts-text-anime-style-1 text-transform-0">Frequently Asked <br> <span>Questions</span>
                        </h2>
                    </div>
                    <div class="right">
                        <p class="desc wow fadeInUp" data-wow-delay=".2s">Find quick answers to common questions about VeriFact AI's algorithms, data usage, privacy protection, and accuracy indices.</p>
                    </div>
                </div>

                <div class="rts-accordion accordion-flush wow fadeInUp" data-wow-delay=".2s" id="rts-accordion">
                    <div class="accordion-item active">
                        <div class="accordion-header" id="first">
                            <h3 class="accordion-button collapse show" data-bs-toggle="collapse" data-bs-target="#item__one" aria-expanded="false" aria-controls="item__one">
                                How does VeriFact AI verify if a news claim is fake?
                            </h3>
                        </div>
                        <div id="item__one" class="accordion-collapse collapse collapse show" data-bs-parent="#rts-accordion">
                            <div class="accordion-body">
                                <p class="desc">VeriFact AI processes articles using lightweight client-side Natural Language Processing (NLP). We measure capitalization density, emotional sentiment levels, punctuation usage, and match text with common conspiracy keywords to calculate credibility scores.</p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <div class="accordion-header" id="two">
                            <h3 class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#item__two" aria-expanded="false" aria-controls="item__two">
                                Is my checked text sent or stored on any server?
                            </h3>
                        </div>
                        <div id="item__two" class="accordion-collapse collapse" data-bs-parent="#rts-accordion">
                            <div class="accordion-body">
                                <p class="desc">No. To ensure maximum data privacy, all verification heuristics run client-side directly within your web browser. Your inputs are never logged, tracked, or sent to external databases.</p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <div class="accordion-header" id="three">
                            <h3 class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#item__three" aria-expanded="false" aria-controls="item__three">
                                Can I integrate VeriFact AI with other tools?
                            </h3>
                        </div>
                        <div id="item__three" class="accordion-collapse collapse" data-bs-parent="#rts-accordion">
                            <div class="accordion-body">
                                <p class="desc">Yes. We support integrations with Slack, browser extensions, and newsroom tools via our unified API endpoints to check social media claims directly in your daily feeds.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- rts faq area end -->

    <!-- rts testimonials area start -->
    <section class="rts-testimonials-area area-2 rts-section-gap2">
        <div class="container">
            <div class="section-title-area center-style">
                <p class="sub-title"><img src="{{ asset(\'assets/images/feature/icon/12.svg\') }}" alt="">Testimonial</p>
                <h2 class="h3 section-title rts-text-anime-style-1 text-transform-0">Trusted Voices That Strengthen <br> <span>Your AI Confidencet</span></h2>
            </div>
            <div class="section-inner mt--60 wow fadeInUp" data-wow-delay=".2s">
                <div class="swiper testimonialsSlider">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <div class="testimonials-wrapper2 top-bg">
                                <div class="wrapper-inner">
                                    <h3 class="h6 title">The flexibility of their pricing helped us scale effortlessly.
                                    </h3>
                                    <p class="desc">The platform is modern and fast, eliminating unnecessary complexity
                                        while providing a smoother, more intuitive workflow that helps teams work
                                        efficiently daily.</p>
                                    <div class="author-area">
                                        <div class="left">
                                            <img src="{{ asset(\'assets/images/testimonials/round-01.svg\') }}" width="61" alt="">
                                            <div class="author-content">
                                                <p class="text">Michael Roberts</p>
                                                <p class="desc">CEO at FinTrack Solutions</p>
                                            </div>
                                        </div>
                                        <div class="right">
                                            <ul class="star-rating">
                                                <li><svg width="19" height="18" viewBox="0 0 19 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M6.03035 5.28164L0.713682 6.05247L0.619515 6.07164C0.476965 6.10948 0.347011 6.18448 0.242925 6.28898C0.13884 6.39347 0.0643514 6.52372 0.027067 6.66641C-0.0102174 6.80911 -0.00896206 6.95915 0.0307049 7.1012C0.0703718 7.24326 0.147029 7.37224 0.252849 7.47497L4.10452 11.2241L3.19618 16.52L3.18535 16.6116C3.17662 16.7591 3.20724 16.9062 3.27406 17.0379C3.34088 17.1696 3.4415 17.2812 3.56563 17.3612C3.68975 17.4413 3.83292 17.4869 3.98047 17.4934C4.12802 17.4999 4.27465 17.4671 4.40535 17.3983L9.16035 14.8983L13.9045 17.3983L13.9878 17.4366C14.1254 17.4908 14.2749 17.5074 14.421 17.4848C14.5671 17.4621 14.7045 17.401 14.8192 17.3077C14.9339 17.2144 15.0216 17.0923 15.0735 16.9538C15.1254 16.8154 15.1396 16.6657 15.1145 16.52L14.2053 11.2241L18.0587 7.47414L18.1237 7.40331C18.2165 7.28895 18.2774 7.15202 18.3001 7.00647C18.3228 6.86092 18.3065 6.71196 18.2529 6.57475C18.1993 6.43754 18.1103 6.317 17.9949 6.2254C17.8796 6.1338 17.742 6.07442 17.5962 6.05331L12.2795 5.28164L9.90285 0.464975C9.83408 0.32542 9.72761 0.207904 9.59551 0.125728C9.4634 0.0435528 9.31093 0 9.15535 0C8.99977 0 8.8473 0.0435528 8.71519 0.125728C8.58308 0.207904 8.47662 0.32542 8.40785 0.464975L6.03035 5.28164Z" fill="#05011C" />
                                                    </svg>
                                                </li>
                                                <li><svg width="19" height="18" viewBox="0 0 19 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M6.03035 5.28164L0.713682 6.05247L0.619515 6.07164C0.476965 6.10948 0.347011 6.18448 0.242925 6.28898C0.13884 6.39347 0.0643514 6.52372 0.027067 6.66641C-0.0102174 6.80911 -0.00896206 6.95915 0.0307049 7.1012C0.0703718 7.24326 0.147029 7.37224 0.252849 7.47497L4.10452 11.2241L3.19618 16.52L3.18535 16.6116C3.17662 16.7591 3.20724 16.9062 3.27406 17.0379C3.34088 17.1696 3.4415 17.2812 3.56563 17.3612C3.68975 17.4413 3.83292 17.4869 3.98047 17.4934C4.12802 17.4999 4.27465 17.4671 4.40535 17.3983L9.16035 14.8983L13.9045 17.3983L13.9878 17.4366C14.1254 17.4908 14.2749 17.5074 14.421 17.4848C14.5671 17.4621 14.7045 17.401 14.8192 17.3077C14.9339 17.2144 15.0216 17.0923 15.0735 16.9538C15.1254 16.8154 15.1396 16.6657 15.1145 16.52L14.2053 11.2241L18.0587 7.47414L18.1237 7.40331C18.2165 7.28895 18.2774 7.15202 18.3001 7.00647C18.3228 6.86092 18.3065 6.71196 18.2529 6.57475C18.1993 6.43754 18.1103 6.317 17.9949 6.2254C17.8796 6.1338 17.742 6.07442 17.5962 6.05331L12.2795 5.28164L9.90285 0.464975C9.83408 0.32542 9.72761 0.207904 9.59551 0.125728C9.4634 0.0435528 9.31093 0 9.15535 0C8.99977 0 8.8473 0.0435528 8.71519 0.125728C8.58308 0.207904 8.47662 0.32542 8.40785 0.464975L6.03035 5.28164Z" fill="#05011C" />
                                                    </svg>
                                                </li>
                                                <li><svg width="19" height="18" viewBox="0 0 19 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M6.03035 5.28164L0.713682 6.05247L0.619515 6.07164C0.476965 6.10948 0.347011 6.18448 0.242925 6.28898C0.13884 6.39347 0.0643514 6.52372 0.027067 6.66641C-0.0102174 6.80911 -0.00896206 6.95915 0.0307049 7.1012C0.0703718 7.24326 0.147029 7.37224 0.252849 7.47497L4.10452 11.2241L3.19618 16.52L3.18535 16.6116C3.17662 16.7591 3.20724 16.9062 3.27406 17.0379C3.34088 17.1696 3.4415 17.2812 3.56563 17.3612C3.68975 17.4413 3.83292 17.4869 3.98047 17.4934C4.12802 17.4999 4.27465 17.4671 4.40535 17.3983L9.16035 14.8983L13.9045 17.3983L13.9878 17.4366C14.1254 17.4908 14.2749 17.5074 14.421 17.4848C14.5671 17.4621 14.7045 17.401 14.8192 17.3077C14.9339 17.2144 15.0216 17.0923 15.0735 16.9538C15.1254 16.8154 15.1396 16.6657 15.1145 16.52L14.2053 11.2241L18.0587 7.47414L18.1237 7.40331C18.2165 7.28895 18.2774 7.15202 18.3001 7.00647C18.3228 6.86092 18.3065 6.71196 18.2529 6.57475C18.1993 6.43754 18.1103 6.317 17.9949 6.2254C17.8796 6.1338 17.742 6.07442 17.5962 6.05331L12.2795 5.28164L9.90285 0.464975C9.83408 0.32542 9.72761 0.207904 9.59551 0.125728C9.4634 0.0435528 9.31093 0 9.15535 0C8.99977 0 8.8473 0.0435528 8.71519 0.125728C8.58308 0.207904 8.47662 0.32542 8.40785 0.464975L6.03035 5.28164Z" fill="#05011C" />
                                                    </svg>
                                                </li>
                                                <li><svg width="19" height="18" viewBox="0 0 19 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M6.03035 5.28164L0.713682 6.05247L0.619515 6.07164C0.476965 6.10948 0.347011 6.18448 0.242925 6.28898C0.13884 6.39347 0.0643514 6.52372 0.027067 6.66641C-0.0102174 6.80911 -0.00896206 6.95915 0.0307049 7.1012C0.0703718 7.24326 0.147029 7.37224 0.252849 7.47497L4.10452 11.2241L3.19618 16.52L3.18535 16.6116C3.17662 16.7591 3.20724 16.9062 3.27406 17.0379C3.34088 17.1696 3.4415 17.2812 3.56563 17.3612C3.68975 17.4413 3.83292 17.4869 3.98047 17.4934C4.12802 17.4999 4.27465 17.4671 4.40535 17.3983L9.16035 14.8983L13.9045 17.3983L13.9878 17.4366C14.1254 17.4908 14.2749 17.5074 14.421 17.4848C14.5671 17.4621 14.7045 17.401 14.8192 17.3077C14.9339 17.2144 15.0216 17.0923 15.0735 16.9538C15.1254 16.8154 15.1396 16.6657 15.1145 16.52L14.2053 11.2241L18.0587 7.47414L18.1237 7.40331C18.2165 7.28895 18.2774 7.15202 18.3001 7.00647C18.3228 6.86092 18.3065 6.71196 18.2529 6.57475C18.1993 6.43754 18.1103 6.317 17.9949 6.2254C17.8796 6.1338 17.742 6.07442 17.5962 6.05331L12.2795 5.28164L9.90285 0.464975C9.83408 0.32542 9.72761 0.207904 9.59551 0.125728C9.4634 0.0435528 9.31093 0 9.15535 0C8.99977 0 8.8473 0.0435528 8.71519 0.125728C8.58308 0.207904 8.47662 0.32542 8.40785 0.464975L6.03035 5.28164Z" fill="#05011C" />
                                                    </svg>
                                                </li>
                                                <li><svg width="19" height="18" viewBox="0 0 19 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M6.03035 5.28164L0.713682 6.05247L0.619515 6.07164C0.476965 6.10948 0.347011 6.18448 0.242925 6.28898C0.13884 6.39347 0.0643514 6.52372 0.027067 6.66641C-0.0102174 6.80911 -0.00896206 6.95915 0.0307049 7.1012C0.0703718 7.24326 0.147029 7.37224 0.252849 7.47497L4.10452 11.2241L3.19618 16.52L3.18535 16.6116C3.17662 16.7591 3.20724 16.9062 3.27406 17.0379C3.34088 17.1696 3.4415 17.2812 3.56563 17.3612C3.68975 17.4413 3.83292 17.4869 3.98047 17.4934C4.12802 17.4999 4.27465 17.4671 4.40535 17.3983L9.16035 14.8983L13.9045 17.3983L13.9878 17.4366C14.1254 17.4908 14.2749 17.5074 14.421 17.4848C14.5671 17.4621 14.7045 17.401 14.8192 17.3077C14.9339 17.2144 15.0216 17.0923 15.0735 16.9538C15.1254 16.8154 15.1396 16.6657 15.1145 16.52L14.2053 11.2241L18.0587 7.47414L18.1237 7.40331C18.2165 7.28895 18.2774 7.15202 18.3001 7.00647C18.3228 6.86092 18.3065 6.71196 18.2529 6.57475C18.1993 6.43754 18.1103 6.317 17.9949 6.2254C17.8796 6.1338 17.742 6.07442 17.5962 6.05331L12.2795 5.28164L9.90285 0.464975C9.83408 0.32542 9.72761 0.207904 9.59551 0.125728C9.4634 0.0435528 9.31093 0 9.15535 0C8.99977 0 8.8473 0.0435528 8.71519 0.125728C8.58308 0.207904 8.47662 0.32542 8.40785 0.464975L6.03035 5.28164Z" fill="#05011C" />
                                                    </svg>
                                                </li>
                                            </ul>
                                            <p class="rating-text">
                                                <span>5.0</span> Ratings
                                            </p>
                                        </div>
                                    </div>
                                    <div class="divider"></div>
                                    <div class="bottom-report">
                                        <h4 class="number">5x</h4>
                                        <div class="chart">
                                            <svg width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M3.9375 31.4997L15.75 19.6872L23.2855 27.2227C25.4641 22.9285 29.0579 19.5177 33.46 17.5662L38.255 15.4312M38.255 15.4312L27.86 11.4395M38.255 15.4312L34.265 25.8262" stroke="#1E0A52" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <p>Business Growth</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="testimonials-wrapper2 bottom-bg">
                                <div class="wrapper-inner">
                                    <h3 class="h6 title">A game-changer for productivity across our projects.
                                    </h3>
                                    <p class="desc">While most tools slow teams down with cluttered workflows, this
                                        platform keeps everything streamlined, intuitive, and remarkably easy, helping
                                        teams work faster.</p>
                                    <div class="author-area">
                                        <div class="left">
                                            <img src="{{ asset(\'assets/images/testimonials/round-01.svg\') }}" width="61" alt="">
                                            <div class="author-content">
                                                <p class="text">Sarah Lee</p>
                                                <p class="desc">Product Manager at BrightTech</p>
                                            </div>
                                        </div>
                                        <div class="right">
                                            <ul class="star-rating">
                                                <li><svg width="19" height="18" viewBox="0 0 19 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M6.03035 5.28164L0.713682 6.05247L0.619515 6.07164C0.476965 6.10948 0.347011 6.18448 0.242925 6.28898C0.13884 6.39347 0.0643514 6.52372 0.027067 6.66641C-0.0102174 6.80911 -0.00896206 6.95915 0.0307049 7.1012C0.0703718 7.24326 0.147029 7.37224 0.252849 7.47497L4.10452 11.2241L3.19618 16.52L3.18535 16.6116C3.17662 16.7591 3.20724 16.9062 3.27406 17.0379C3.34088 17.1696 3.4415 17.2812 3.56563 17.3612C3.68975 17.4413 3.83292 17.4869 3.98047 17.4934C4.12802 17.4999 4.27465 17.4671 4.40535 17.3983L9.16035 14.8983L13.9045 17.3983L13.9878 17.4366C14.1254 17.4908 14.2749 17.5074 14.421 17.4848C14.5671 17.4621 14.7045 17.401 14.8192 17.3077C14.9339 17.2144 15.0216 17.0923 15.0735 16.9538C15.1254 16.8154 15.1396 16.6657 15.1145 16.52L14.2053 11.2241L18.0587 7.47414L18.1237 7.40331C18.2165 7.28895 18.2774 7.15202 18.3001 7.00647C18.3228 6.86092 18.3065 6.71196 18.2529 6.57475C18.1993 6.43754 18.1103 6.317 17.9949 6.2254C17.8796 6.1338 17.742 6.07442 17.5962 6.05331L12.2795 5.28164L9.90285 0.464975C9.83408 0.32542 9.72761 0.207904 9.59551 0.125728C9.4634 0.0435528 9.31093 0 9.15535 0C8.99977 0 8.8473 0.0435528 8.71519 0.125728C8.58308 0.207904 8.47662 0.32542 8.40785 0.464975L6.03035 5.28164Z" fill="#05011C" />
                                                    </svg>
                                                </li>
                                                <li><svg width="19" height="18" viewBox="0 0 19 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M6.03035 5.28164L0.713682 6.05247L0.619515 6.07164C0.476965 6.10948 0.347011 6.18448 0.242925 6.28898C0.13884 6.39347 0.0643514 6.52372 0.027067 6.66641C-0.0102174 6.80911 -0.00896206 6.95915 0.0307049 7.1012C0.0703718 7.24326 0.147029 7.37224 0.252849 7.47497L4.10452 11.2241L3.19618 16.52L3.18535 16.6116C3.17662 16.7591 3.20724 16.9062 3.27406 17.0379C3.34088 17.1696 3.4415 17.2812 3.56563 17.3612C3.68975 17.4413 3.83292 17.4869 3.98047 17.4934C4.12802 17.4999 4.27465 17.4671 4.40535 17.3983L9.16035 14.8983L13.9045 17.3983L13.9878 17.4366C14.1254 17.4908 14.2749 17.5074 14.421 17.4848C14.5671 17.4621 14.7045 17.401 14.8192 17.3077C14.9339 17.2144 15.0216 17.0923 15.0735 16.9538C15.1254 16.8154 15.1396 16.6657 15.1145 16.52L14.2053 11.2241L18.0587 7.47414L18.1237 7.40331C18.2165 7.28895 18.2774 7.15202 18.3001 7.00647C18.3228 6.86092 18.3065 6.71196 18.2529 6.57475C18.1993 6.43754 18.1103 6.317 17.9949 6.2254C17.8796 6.1338 17.742 6.07442 17.5962 6.05331L12.2795 5.28164L9.90285 0.464975C9.83408 0.32542 9.72761 0.207904 9.59551 0.125728C9.4634 0.0435528 9.31093 0 9.15535 0C8.99977 0 8.8473 0.0435528 8.71519 0.125728C8.58308 0.207904 8.47662 0.32542 8.40785 0.464975L6.03035 5.28164Z" fill="#05011C" />
                                                    </svg>
                                                </li>
                                                <li><svg width="19" height="18" viewBox="0 0 19 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M6.03035 5.28164L0.713682 6.05247L0.619515 6.07164C0.476965 6.10948 0.347011 6.18448 0.242925 6.28898C0.13884 6.39347 0.0643514 6.52372 0.027067 6.66641C-0.0102174 6.80911 -0.00896206 6.95915 0.0307049 7.1012C0.0703718 7.24326 0.147029 7.37224 0.252849 7.47497L4.10452 11.2241L3.19618 16.52L3.18535 16.6116C3.17662 16.7591 3.20724 16.9062 3.27406 17.0379C3.34088 17.1696 3.4415 17.2812 3.56563 17.3612C3.68975 17.4413 3.83292 17.4869 3.98047 17.4934C4.12802 17.4999 4.27465 17.4671 4.40535 17.3983L9.16035 14.8983L13.9045 17.3983L13.9878 17.4366C14.1254 17.4908 14.2749 17.5074 14.421 17.4848C14.5671 17.4621 14.7045 17.401 14.8192 17.3077C14.9339 17.2144 15.0216 17.0923 15.0735 16.9538C15.1254 16.8154 15.1396 16.6657 15.1145 16.52L14.2053 11.2241L18.0587 7.47414L18.1237 7.40331C18.2165 7.28895 18.2774 7.15202 18.3001 7.00647C18.3228 6.86092 18.3065 6.71196 18.2529 6.57475C18.1993 6.43754 18.1103 6.317 17.9949 6.2254C17.8796 6.1338 17.742 6.07442 17.5962 6.05331L12.2795 5.28164L9.90285 0.464975C9.83408 0.32542 9.72761 0.207904 9.59551 0.125728C9.4634 0.0435528 9.31093 0 9.15535 0C8.99977 0 8.8473 0.0435528 8.71519 0.125728C8.58308 0.207904 8.47662 0.32542 8.40785 0.464975L6.03035 5.28164Z" fill="#05011C" />
                                                    </svg>
                                                </li>
                                                <li><svg width="19" height="18" viewBox="0 0 19 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M6.03035 5.28164L0.713682 6.05247L0.619515 6.07164C0.476965 6.10948 0.347011 6.18448 0.242925 6.28898C0.13884 6.39347 0.0643514 6.52372 0.027067 6.66641C-0.0102174 6.80911 -0.00896206 6.95915 0.0307049 7.1012C0.0703718 7.24326 0.147029 7.37224 0.252849 7.47497L4.10452 11.2241L3.19618 16.52L3.18535 16.6116C3.17662 16.7591 3.20724 16.9062 3.27406 17.0379C3.34088 17.1696 3.4415 17.2812 3.56563 17.3612C3.68975 17.4413 3.83292 17.4869 3.98047 17.4934C4.12802 17.4999 4.27465 17.4671 4.40535 17.3983L9.16035 14.8983L13.9045 17.3983L13.9878 17.4366C14.1254 17.4908 14.2749 17.5074 14.421 17.4848C14.5671 17.4621 14.7045 17.401 14.8192 17.3077C14.9339 17.2144 15.0216 17.0923 15.0735 16.9538C15.1254 16.8154 15.1396 16.6657 15.1145 16.52L14.2053 11.2241L18.0587 7.47414L18.1237 7.40331C18.2165 7.28895 18.2774 7.15202 18.3001 7.00647C18.3228 6.86092 18.3065 6.71196 18.2529 6.57475C18.1993 6.43754 18.1103 6.317 17.9949 6.2254C17.8796 6.1338 17.742 6.07442 17.5962 6.05331L12.2795 5.28164L9.90285 0.464975C9.83408 0.32542 9.72761 0.207904 9.59551 0.125728C9.4634 0.0435528 9.31093 0 9.15535 0C8.99977 0 8.8473 0.0435528 8.71519 0.125728C8.58308 0.207904 8.47662 0.32542 8.40785 0.464975L6.03035 5.28164Z" fill="#05011C" />
                                                    </svg>
                                                </li>
                                                <li><svg width="19" height="18" viewBox="0 0 19 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M6.03035 5.28164L0.713682 6.05247L0.619515 6.07164C0.476965 6.10948 0.347011 6.18448 0.242925 6.28898C0.13884 6.39347 0.0643514 6.52372 0.027067 6.66641C-0.0102174 6.80911 -0.00896206 6.95915 0.0307049 7.1012C0.0703718 7.24326 0.147029 7.37224 0.252849 7.47497L4.10452 11.2241L3.19618 16.52L3.18535 16.6116C3.17662 16.7591 3.20724 16.9062 3.27406 17.0379C3.34088 17.1696 3.4415 17.2812 3.56563 17.3612C3.68975 17.4413 3.83292 17.4869 3.98047 17.4934C4.12802 17.4999 4.27465 17.4671 4.40535 17.3983L9.16035 14.8983L13.9045 17.3983L13.9878 17.4366C14.1254 17.4908 14.2749 17.5074 14.421 17.4848C14.5671 17.4621 14.7045 17.401 14.8192 17.3077C14.9339 17.2144 15.0216 17.0923 15.0735 16.9538C15.1254 16.8154 15.1396 16.6657 15.1145 16.52L14.2053 11.2241L18.0587 7.47414L18.1237 7.40331C18.2165 7.28895 18.2774 7.15202 18.3001 7.00647C18.3228 6.86092 18.3065 6.71196 18.2529 6.57475C18.1993 6.43754 18.1103 6.317 17.9949 6.2254C17.8796 6.1338 17.742 6.07442 17.5962 6.05331L12.2795 5.28164L9.90285 0.464975C9.83408 0.32542 9.72761 0.207904 9.59551 0.125728C9.4634 0.0435528 9.31093 0 9.15535 0C8.99977 0 8.8473 0.0435528 8.71519 0.125728C8.58308 0.207904 8.47662 0.32542 8.40785 0.464975L6.03035 5.28164Z" fill="#05011C" />
                                                    </svg>
                                                </li>
                                            </ul>
                                            <p class="rating-text">
                                                <span>5.0</span> Ratings
                                            </p>
                                        </div>
                                    </div>
                                    <div class="divider"></div>
                                    <div class="bottom-report">
                                        <h4 class="number">5x</h4>
                                        <div class="chart">
                                            <svg width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M3.9375 31.4997L15.75 19.6872L23.2855 27.2227C25.4641 22.9285 29.0579 19.5177 33.46 17.5662L38.255 15.4312M38.255 15.4312L27.86 11.4395M38.255 15.4312L34.265 25.8262" stroke="#1E0A52" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <p>Business Growth</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="navigation-button-area">
                    <div class="swiper-btn swiper-btn-prev"><i class="fa-sharp fa-regular fa-arrow-left"></i></div>
                    <div class="swiper-btn swiper-btn-next"><i class="fa-sharp fa-regular fa-arrow-right"></i></div>
                </div>
            </div>
        </div>
    </section>
    <!-- rts testimonials area end -->

    <!-- rts cta area start -->
    <section class="rts-cta-area area-2">
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
