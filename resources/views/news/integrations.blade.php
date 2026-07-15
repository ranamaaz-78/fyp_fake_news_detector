@extends('layouts.frontend')

@section('title', 'Integration - VeriFact AI')

@section('bodyClass', 'innerpage')

@section('content')
<!-- rts features area start -->
    <section class="rts-feature-area inner-one rts-section-gap2" data-bg-src="{{ asset(\'assets/images/banner/10.webp\') }}">
        <div class="container-1320">
            <div class="section-title-area center-style">
                <p class="sub-title"><span class="dot"></span>FEATURE</p>
                <h2 class="h3 section-title rts-text-anime-style-1 text-transform-0">One platform connected to <br> <span> everything you need.</span></h2>
                <p class="desc wow fadeInUp" data-wow-delay=".4s">Automate processes, sync data, and keep work moving across tools your <br> team already relies on every day.</p>
            </div>
        </div>
    </section>
    <!-- rts features area end -->

    <!-- rts integrations area start -->
    <section class="rts-integrations-area rts-section-gap2">
        <div class="container-1320">
            <div class="section-inner">
                <div class="row g-28">
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay=".2s">
                        <div class="rts-integration-wrapper">
                            <div class="top-area">
                                <div class="logo-area">
                                    <img src="{{ asset(\'assets/images/integrations/1.svg\') }}" alt="">
                                    <h3 class="title">Slack Bot</h3>
                                </div>
                            </div>
                            <p class="desc">Verify news claims directly in your communication workspace with Slack rumor alerts.</p>
                            <a href="{{ route('contact') }}" class="rts-btn btn-primary">
                                Setup Integration
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20.75 11.7266L3.75 11.7266" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M20.7531 11.725L14.7031 17.75" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay=".4s">
                        <div class="rts-integration-wrapper">
                            <div class="top-area">
                                <div class="logo-area">
                                    <img src="{{ asset(\'assets/images/integrations/2.svg\') }}" alt="">
                                    <h3 class="title">Twitter / X Monitor</h3>
                                </div>
                            </div>
                            <p class="desc">Monitor social media feeds and automatically identify trending disinformation campaigns.</p>
                            <a href="{{ route('contact') }}" class="rts-btn btn-primary">
                                Setup Integration
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20.75 11.7266L3.75 11.7266" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M20.7531 11.725L14.7031 17.75" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay=".6s">
                        <div class="rts-integration-wrapper">
                            <div class="top-area">
                                <div class="logo-area">
                                    <img src="{{ asset(\'assets/images/integrations/3.svg\') }}" alt="">
                                    <h3 class="title">Chrome Extension</h3>
                                </div>
                            </div>
                            <p class="desc">Get real-time trust ratings and clickbait alerts directly as you browse the web.</p>
                            <a href="{{ route('contact') }}" class="rts-btn btn-primary">
                                Setup Integration
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20.75 11.7266L3.75 11.7266" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M20.7531 11.725L14.7031 17.75" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay=".2s">
                        <div class="rts-integration-wrapper">
                            <div class="top-area">
                                <div class="logo-area">
                                    <img src="{{ asset(\'assets/images/integrations/4.svg\') }}" alt="">
                                    <h3 class="title">WordPress Plugin</h3>
                                </div>
                            </div>
                            <p class="desc">Filter out reader comments and automatically check credibility of submitted links.</p>
                            <a href="{{ route('contact') }}" class="rts-btn btn-primary">
                                Setup Integration
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20.75 11.7266L3.75 11.7266" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M20.7531 11.725L14.7031 17.75" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay=".4s">
                        <div class="rts-integration-wrapper">
                            <div class="top-area">
                                <div class="logo-area">
                                    <img src="{{ asset(\'assets/images/integrations/5.svg\') }}" alt="">
                                    <h3 class="title">Unified Newsroom API</h3>
                                </div>
                            </div>
                            <p class="desc">Plug VeriFact AI algorithms directly into your journalistic newsroom workflow.</p>
                            <a href="{{ route('contact') }}" class="rts-btn btn-primary">
                                Setup Integration
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20.75 11.7266L3.75 11.7266" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M20.7531 11.725L14.7031 17.75" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay=".6s">
                        <div class="rts-integration-wrapper">
                            <div class="top-area">
                                <div class="logo-area">
                                    <img src="{{ asset(\'assets/images/integrations/6.svg\') }}" alt="">
                                    <h3 class="title">Telegram Bot</h3>
                                </div>
                            </div>
                            <p class="desc">Forward suspicious messages to our automated verification chat bot instantly.</p>
                            <a href="{{ route('contact') }}" class="rts-btn btn-primary">
                                Setup Integration
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20.75 11.7266L3.75 11.7266" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M20.7531 11.725L14.7031 17.75" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay=".2s">
                        <div class="rts-integration-wrapper">
                            <div class="top-area">
                                <div class="logo-area">
                                    <img src="{{ asset(\'assets/images/integrations/2.svg\') }}" alt="">
                                    <h3 class="title">Zapier</h3>
                                </div>
                                <a href="#">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <mask style="mask-type:luminance" maskUnits="userSpaceOnUse" x="2" y="2" width="20" height="20">
                                            <path d="M2.40039 2.40156H21.6004V21.6016H2.40039V2.40156Z" fill="white" />
                                        </mask>
                                        <g mask="url(#mask0_9662_6921)">
                                            <path d="M19.3567 10.5203V6.87036C19.3567 5.62769 18.3494 4.62036 17.1067 4.62036H13.4816M9.05038 14.9516L18.71 5.29195M20.8504 14.9516V16.0376C20.8504 17.2311 20.3763 18.3757 19.5324 19.2196L19.2184 19.5336C18.3745 20.3775 17.2299 20.8516 16.0364 20.8516H7.96434C6.77087 20.8516 5.62625 20.3775 4.78239 19.5336L4.46844 19.2196C3.6245 18.3757 3.15039 17.2311 3.15039 16.0376V7.96551C3.15039 6.77204 3.6245 5.62746 4.46844 4.78356L4.78239 4.46957C5.62625 3.62567 6.77087 3.15156 7.96434 3.15156H9.05038" stroke="#1E3A8A" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                        </g>
                                    </svg>
                                </a>
                            </div>
                            <p class="desc">Keep your team updated on every activity with instant Slack notifications.</p>
                            <a href="single-integration.html" class="rts-btn btn-primary">
                                View Integration
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20.75 11.7266L3.75 11.7266" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M20.7531 11.725L14.7031 17.75" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay=".4s">
                        <div class="rts-integration-wrapper">
                            <div class="top-area">
                                <div class="logo-area">
                                    <img src="{{ asset(\'assets/images/integrations/3.svg\') }}" alt="">
                                    <h3 class="title">Stripe</h3>
                                </div>
                                <a href="#">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <mask style="mask-type:luminance" maskUnits="userSpaceOnUse" x="2" y="2" width="20" height="20">
                                            <path d="M2.40039 2.40156H21.6004V21.6016H2.40039V2.40156Z" fill="white" />
                                        </mask>
                                        <g mask="url(#mask0_9662_6921)">
                                            <path d="M19.3567 10.5203V6.87036C19.3567 5.62769 18.3494 4.62036 17.1067 4.62036H13.4816M9.05038 14.9516L18.71 5.29195M20.8504 14.9516V16.0376C20.8504 17.2311 20.3763 18.3757 19.5324 19.2196L19.2184 19.5336C18.3745 20.3775 17.2299 20.8516 16.0364 20.8516H7.96434C6.77087 20.8516 5.62625 20.3775 4.78239 19.5336L4.46844 19.2196C3.6245 18.3757 3.15039 17.2311 3.15039 16.0376V7.96551C3.15039 6.77204 3.6245 5.62746 4.46844 4.78356L4.78239 4.46957C5.62625 3.62567 6.77087 3.15156 7.96434 3.15156H9.05038" stroke="#1E3A8A" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                        </g>
                                    </svg>
                                </a>
                            </div>
                            <p class="desc">Keep your team updated on every activity with instant Slack notifications.</p>
                            <a href="single-integration.html" class="rts-btn btn-primary">
                                View Integration
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20.75 11.7266L3.75 11.7266" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M20.7531 11.725L14.7031 17.75" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
    <!-- rts integration area end -->

    <!-- rts faq area start -->
    <section class="rts-faq-area area-2 rts-section-gap2" data-bg-src="{{ asset(\'assets/images/faq/faq-bg.webp\') }}">
        <div class="container">
            <div class="section-inner">
                <div class="section-title-area">
                    <div class="left">
                        <p class="sub-title"><img src="{{ asset(\'assets/images/feature/icon/11.svg\') }}" alt="">FAQ</p>
                        <h2 class="h3 section-title rts-text-anime-style-1 text-transform-0">Trusted Answers That Support <br> <span>Your AI Workflow</span>
                        </h2>
                    </div>
                    <div class="right">
                        <p class="desc wow fadeInUp" data-wow-delay=".2s">Find clear answers to common questions about features, pricing, security, and
                            support , so you can move forward with confidence.</p>
                    </div>
                </div>

                <div class="rts-accordion accordion-flush wow fadeInUp" data-wow-delay=".2s" id="rts-accordion">
                    <div class="accordion-item active">
                        <div class="accordion-header" id="first">
                            <h3 class="accordion-button collapse show" data-bs-toggle="collapse" data-bs-target="#item__one" aria-expanded="false" aria-controls="item__one">
                                What is this project management platform used for?
                            </h3>
                        </div>
                        <div id="item__one" class="accordion-collapse collapse collapse show" data-bs-parent="#rts-accordion">
                            <div class="accordion-body">
                                <p class="desc">I specialize in UI/UX design, Webflow/Next.js development, brand
                                    identity, and landing pages built for conversion.</p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <div class="accordion-header" id="two">
                            <h3 class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#item__two" aria-expanded="false" aria-controls="item__two">
                                Who is this platform best suited for?
                            </h3>
                        </div>
                        <div id="item__two" class="accordion-collapse collapse" data-bs-parent="#rts-accordion">
                            <div class="accordion-body">
                                <p class="desc">I specialize in UI/UX design, Webflow/Next.js development, brand
                                    identity, and landing pages built for conversion.</p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <div class="accordion-header" id="three">
                            <h3 class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#item__three" aria-expanded="false" aria-controls="item__three">
                                Can I manage multiple projects at the same time?
                            </h3>
                        </div>
                        <div id="item__three" class="accordion-collapse collapse" data-bs-parent="#rts-accordion">
                            <div class="accordion-body">
                                <p class="desc">I specialize in UI/UX design, Webflow/Next.js development, brand
                                    identity, and landing pages built for conversion.</p>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <div class="accordion-header" id="four">
                            <h3 class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#item__four" aria-expanded="false" aria-controls="item__four">
                                Does it support team collaboration?
                            </h3>
                        </div>
                        <div id="item__four" class="accordion-collapse collapse" data-bs-parent="#rts-accordion">
                            <div class="accordion-body">
                                <p class="desc">I specialize in UI/UX design, Webflow/Next.js development, brand
                                    identity, and landing pages built for conversion.</p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <div class="accordion-header" id="five">
                            <h3 class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#item__five" aria-expanded="false" aria-controls="item__five">
                                Is the platform suitable for remote or distributed teams?
                            </h3>
                        </div>
                        <div id="item__five" class="accordion-collapse collapse" data-bs-parent="#rts-accordion">
                            <div class="accordion-body">
                                <p class="desc">I specialize in UI/UX design, Webflow/Next.js development, brand
                                    identity, and landing pages built for conversion.</p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <div class="accordion-header" id="six">
                            <h3 class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#item__six" aria-expanded="false" aria-controls="item__six">
                                Can I customize workflows to match my process?
                            </h3>
                        </div>
                        <div id="item__six" class="accordion-collapse collapse" data-bs-parent="#rts-accordion">
                            <div class="accordion-body">
                                <p class="desc">I specialize in UI/UX design, Webflow/Next.js development, brand
                                    identity, and landing pages built for conversion.</p>
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
