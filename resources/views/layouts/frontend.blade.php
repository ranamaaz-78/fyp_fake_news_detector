<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/fav.svg') }}">
    <title>@yield('title', 'VeriFact AI - Fact-Checking & Fake News Detection')</title>
    
    <!-- swiper css -->
    <link rel="preload" href="{{ asset('assets/css/plugins/swiper.min.css') }}" as="style">
    <!-- Custom Font css -->
    <link rel="preload" href="{{ asset('assets/fonts/custom-font.css') }}" as="style">
    <!-- magnific popup css -->
    <link rel="preload" href="{{ asset('assets/css/plugins/magnific-popup.css') }}" as="style">
    <!-- metismenu css -->
    <link rel="preload" href="{{ asset('assets/css/plugins/metismenu.css') }}" as="style">
    <!-- bootstrap css -->
    <link rel="preload" href="{{ asset('assets/css/vendor/bootstrap.min.css') }}" as="style">
    <!-- bootstrap css -->
    <link rel="preload" href="{{ asset('assets/css/vendor/animate.css') }}" as="style">
    <!-- odometer css -->
    <link rel="preload" href="{{ asset('assets/css/plugins/odometer.css') }}" as="style">
    <!-- fontawesome css -->
    <link rel="preload" href="{{ asset('assets/css/plugins/fontawesome.min.css') }}" as="style">
    <!-- Nice Select css -->
    <link rel="preload" href="{{ asset('assets/css/plugins/nice-select.css') }}" as="style">
    <!-- Custom css -->
    <link rel="preload" href="{{ asset('assets/css/style.css') }}" as="style">

    <link rel="stylesheet" href="{{ asset('assets/css/plugins/swiper.min.css') }}">
    <!-- Custom Font css -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/custom-font.css') }}">
    <!-- magnific popup css -->
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/magnific-popup.css') }}">
    <!-- metismenu css -->
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/metismenu.css') }}">
    <!-- bootstrap css -->
    <link rel="stylesheet" href="{{ asset('assets/css/vendor/bootstrap.min.css') }}">
    <!-- bootstrap css -->
    <link rel="stylesheet" href="{{ asset('assets/css/vendor/animate.css') }}">
    <!-- odometer css -->
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/odometer.css') }}">
    <!-- fontawesome css -->
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/fontawesome.min.css') }}">
    <!-- Nice Select css -->
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/nice-select.css') }}">
    <!-- Custom css -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <!-- Verifier Custom css -->
    <link rel="stylesheet" href="{{ asset('assets/css/verifier.css') }}">
</head>

<body class="@yield('bodyClass', 'home-six')">

    <div class="loader-wrapper">
        <div class="loader"></div>
        <div class="loader-section section-left"></div>
        <div class="loader-section section-right"></div>
    </div>

    <!-- header area start -->
    <header class="header-style-one header-two header--sticky">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="header-style-one-wrapper">
                        <div class="left-area">
                            <div class="logo-area">
                                <a href="{{ route('home') }}" class="logo">
                                    <img class="light" src="{{ asset('assets/images/navwhitelogo.svg') }}" alt="logo">
                                    <img class="dark" src="{{ asset('assets/images/navcolorlogo.svg') }}" alt="logo">
                                </a>
                            </div>
                        </div>
                        <nav class="main-nav-area">
                            <ul class="list-unstyled rts-desktop-menu">
                                <li class="menu-item"><a class="main-element without-arrow" href="{{ route('home') }}">Home</a></li>
                                <li class="menu-item"><a class="main-element without-arrow" href="{{ route('features') }}">Features</a></li>
                                <li class="menu-item"><a class="main-element without-arrow" href="{{ route('how-it-works') }}">How It Works</a></li>
                                <li class="menu-item"><a class="main-element without-arrow" href="{{ route('integrations') }}">Integrations</a></li>
                                <li class="menu-item"><a class="main-element without-arrow" href="{{ route('faq') }}">FAQ</a></li>
                                <li class="menu-item"><a class="main-element without-arrow" href="{{ route('contact') }}">Contact</a></li>
                            </ul>
                        </nav>
                        <div class="button-area-start">
                            @auth
                                <a href="{{ route('dashboard') }}" class="rts-btn btn-primary btn-border icon-prev">
                                    <span class="icon">
                                        <svg width="19" height="8" viewBox="0 0 19 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M17.9 0.900391L0.900024 0.900391" stroke="#1E3A8A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M17.9031 0.900781L11.8531 6.92578" stroke="#1E3A8A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                    Dashboard
                                </a>
                                <form method="POST" action="{{ route('logout') }}" style="display:inline; margin-left:10px;">
                                    @csrf
                                    <button type="submit" class="rts-btn btn-primary" style="padding: 10px 18px; border-radius: 6px; font-size: 14px;">Logout</button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="rts-btn btn-primary btn-border icon-prev" style="margin-right: 8px;">
                                    Sign In
                                </a>
                                <a href="{{ route('register') }}" class="rts-btn btn-primary">
                                    Get Started
                                </a>
                            @endauth
                            
                            <div class="menu-btn menu-btn-toggle radius-6" id="menu-btn">
                                <svg width="20" height="16" viewBox="0 0 20 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect y="14" width="20" height="2" fill="#FFFFFF"></rect>
                                    <rect y="7" width="20" height="2" fill="#FFFFFF"></rect>
                                    <rect width="20" height="2" fill="#FFFFFF"></rect>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- header area end -->

    @yield('content')

    <!-- rts footer area start -->
    <div class="rts-footer-area-one footer-two pt--200 pb--30" data-bg-src="{{ asset('assets/images/footer/bg-01.webp') }}">
        <div class="container">
            <div class="footer-inner">
                <div class="single-footer-widget-one logo-area">
                    <a href="{{ route('home') }}" class="logo">
                        <img src="{{ asset('assets/images/whitelogo.svg') }}" alt="logo" style="max-height: 45px;">
                    </a>
                    <p class="desc">Verify Smarter. Expose Falsehoods. AI-Powered Credibility Diagnostics for Everyone.</p>
                    <ul class="social-area">
                        <li><a href="https://www.ucp.edu.pk/" target="_blank" rel="noopener noreferrer" aria-label="University of Central Punjab"><i class="fa-solid fa-graduation-cap"></i></a></li>
                        <li><a href="{{ route('contact') }}" aria-label="Contact us"><i class="fa-solid fa-envelope"></i></a></li>
                        <li><a href="{{ route('faq') }}" aria-label="Frequently asked questions"><i class="fa-solid fa-circle-question"></i></a></li>
                    </ul>
                </div>
                <div class="single-footer-widget-one essential-links">
                    <h2 class="title">Features</h2>
                    <ul>
                        <li><a href="{{ route('features') }}">Heuristics</a></li>
                        <li><a href="{{ route('how-it-works') }}">How It Works</a></li>
                        <li><a href="{{ route('integrations') }}">Integrations</a></li>
                    </ul>
                </div>
                <div class="single-footer-widget-one essential-links">
                    <h2 class="title">Company</h2>
                    <ul>
                        <li><a href="{{ route('contact') }}">Contact</a></li>
                        @guest
                            <li><a href="{{ route('register') }}">Sign Up</a></li>
                            <li><a href="{{ route('login') }}">Sign In</a></li>
                        @else
                            <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        @endguest
                        <li><a href="{{ route('faq') }}">FAQ</a></li>
                    </ul>
                </div>
                <div class="single-footer-widget-one get-in-touch">
                    <h2 class="title">Any Questions?</h2>
                    <ul>
                        <li><a href="{{ route('contact') }}">Send us a message</a></li>
                        <li><a href="https://www.ucp.edu.pk/" target="_blank" rel="noopener noreferrer">University of Central Punjab</a></li>
                        <li>1-Khayaban-e-Jinnah Road, Johar Town, Lahore, Pakistan</li>
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <!-- copyright area start -->
                    <div class="copyright-area-start">
                        <p>© {{ date('Y') }} VeriFact AI. All rights reserved.</p>
                        <p>Built by Maaz Naveed, Jazil Mehmood &amp; Muhammad Abrar</p>
                        <p>UCP Lahore &mdash; supervised by Prof. Muzammil Sadiq &mdash; Group G1F22FYPCS016</p>
                    </div>
                    <!-- copyright area end -->
                </div>
            </div>
        </div>
    </div>
    <!-- rts footer area end -->

    <!-- side bar menu start -->
    <div id="side-bar" class="side-bar header-two">
        <button class="close-icon-menu"><i class="fa-sharp fa-thin fa-xmark"></i></button>
        <a class="logo" href="{{ route('home') }}" style="margin-top: 20px; display: inline-block;">
            <img src="{{ asset('assets/images/navwhitelogo.svg') }}" alt="logo" style="max-height: 45px;">
        </a>
        <div class="mobile-menu-main">
            <nav class="nav-main mainmenu-nav mt--30">
                <ul class="mainmenu metismenu" id="mobile-menu-active">
                    <li><a class="mobile-menu-link" href="{{ route('home') }}">Home</a></li>
                    <li><a class="mobile-menu-link" href="{{ route('features') }}">Features</a></li>
                    <li><a class="mobile-menu-link" href="{{ route('how-it-works') }}">How It Works</a></li>
                    <li><a class="mobile-menu-link" href="{{ route('integrations') }}">Integrations</a></li>
                    <li><a class="mobile-menu-link" href="{{ route('faq') }}">FAQ</a></li>
                    <li><a class="mobile-menu-link" href="{{ route('contact') }}">Contact Us</a></li>
                </ul>
            </nav>
            <div class="follow-us mt-4">
                <ul>
                    <li><a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a></li>
                    <li><a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a></li>
                    <li><a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a></li>
                    <li><a href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a></li>
                </ul>
            </div>
        </div>
    </div>
    <!-- side bar menu end -->

    <div id="anywhere-home" class=""></div>

    <!-- progress area start -->
    <div class="progress-wrap">
        <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"
                style="transition: stroke-dashoffset 10ms linear 0s; stroke-dasharray: 307.919, 307.919; stroke-dashoffset: 307.919;">
            </path>
        </svg>
    </div>
    <!-- progress area end -->

    <!-- jquery js -->
    <script defer src="{{ asset('assets/js/plugins/jquery.min.js') }}"></script>
    <script defer src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
    <script defer src="{{ asset('assets/js/plugins/metismenu.js') }}"></script>
    <script defer src="{{ asset('assets/js/vendor/jqueryui.js') }}"></script>
    <script defer src="{{ asset('assets/js/vendor/waypoint.js') }}"></script>
    <script defer src="{{ asset('assets/js/plugins/swiper.js') }}"></script>
    <script defer src="{{ asset('assets/js/plugins/gsap.min.js') }}"></script>
    <script defer src="{{ asset('assets/js/vendor/split-text.js') }}"></script>
    <script defer src="{{ asset('assets/js/plugins/scrolltigger.js') }}"></script>
    <script defer src="{{ asset('assets/js/plugins/smoothscroll.js') }}"></script>
    <script defer src="{{ asset('assets/js/vendor/wow.js') }}"></script>
    <script defer src="{{ asset('assets/js/plugins/odometer.js') }}"></script>
    <script defer src="{{ asset('assets/js/plugins/magnific-popup.js') }}"></script>
    <script defer src="{{ asset('assets/js/plugins/isotop.js') }}"></script>
    <script defer src="{{ asset('assets/js/plugins/contact-form.js') }}"></script>
    <script defer src="{{ asset('assets/js/main.js') }}"></script>
    <script defer src="{{ asset('assets/js/verifier.js') }}"></script>

</body>
</html>
