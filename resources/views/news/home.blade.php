@extends('layouts.frontend')

@section('title', 'VeriFact AI - Deep Learning Powered Fact-Checking & Fake News Detection')

@section('content')
    <!-- rts banner area start -->
    <section id="home" class="rts-banner-area-six" data-bg-src="{{ asset('assets/images/banner/banner-bg-03.png') }}">
        <div class="container-1340">
            <div class="banner-inner">
                <div class="section-title-area">
                    <h1 class="section-title rts-text-anime-style-1 text-transform-0">Verify News & <br>Detect Falsehoods.</h1>
                    <p class="desc wow fadeInUp" data-wow-delay="0.9s">
                        Instantly check the credibility of any news article, social media claim, or URL. Empower your information hygiene with cutting-edge NLP analysis.
                    </p>
                </div>
                <div class="button-area wow fadeInUp" data-wow-delay="1.5s">
                    <a href="#verifier" class="rts-btn btn-primary">
                        Analyze News Now
                        <svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2.78609 0.636774C3.42696 1.41352 5.13873 1.95475 6.7051 2.30304C8.72196 2.75133 10.7973 2.8843 12.8309 2.61539C14.3541 2.41363 16.072 2.00892 17.0527 1.19981M14.9104 15.3317C14.2695 14.555 14.0633 12.7716 14.0189 11.1676C13.962 9.10235 14.2256 7.03943 14.8759 5.09401C15.3633 3.63687 16.087 2.02707 17.0676 1.21797M17.0602 1.2089L0.63638 14.7596" stroke="white" stroke-width="2" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
        <div class="mobile-screen wow fadeInUp" data-wow-delay="0.3s">
            <img src="{{ asset('assets/images/banner/mobile.webp') }}" alt="mobile">
        </div>
    </section>
    <!-- rts banner area end -->

    <!-- Verifier Widget Section Start -->
    <section id="verifier" class="verifier-section">
        <div class="container-1340">
            <div id="verifierWidget" class="verifier-container">
                <div class="verifier-title-block">
                    <h3>Real-Time News Verifier</h3>
                    <p>Paste an article paragraph, headline, or domain URL to run an instant credibility and sensationalism check.</p>
                </div>
                <form class="verifier-form">
                    <!-- Tabs -->
                    <div class="verifier-tabs mb-4 d-flex justify-content-center gap-3">
                        <button type="button" class="verifier-tab-btn active" data-tab="text">
                            <i class="fa-solid fa-file-lines"></i> Text Check
                        </button>
                        <button type="button" class="verifier-tab-btn" data-tab="url">
                            <i class="fa-solid fa-link"></i> URL Link Check
                        </button>
                        <button type="button" class="verifier-tab-btn" data-tab="image">
                            <i class="fa-solid fa-image"></i> Image Check
                        </button>
                    </div>

                    <!-- Tab Contents -->
                    <div class="verifier-tab-content active" id="tab-text">
                        <textarea id="newsText" placeholder="Paste the news story, statement, or article here... (e.g., 'SHOCKING: Secrets exposed! The government is hiding a miracle vaccine cure!!!' or standard factual report text)"></textarea>
                    </div>
                    <div class="verifier-tab-content d-none" id="tab-url">
                        <input type="url" id="newsUrl" class="verifier-input mb-4" placeholder="Paste the news article link here... (e.g., https://nytimes.com/some-article-url)">
                    </div>
                    <div class="verifier-tab-content d-none" id="tab-image">
                        <div class="image-upload-wrapper text-center p-5 mb-4 cursor-pointer" id="dragBoxContainer">
                            <input type="file" id="newsImage" accept="image/*" class="d-none">
                            <div id="imageDragBox" class="py-3">
                                <i class="fa-solid fa-cloud-arrow-up text-primary mb-3" style="font-size: 40px;"></i>
                                <h5 class="mb-2" style="color: #1a2d54;">Choose image file or drag here</h5>
                                <p class="text-sm mb-0" style="color: #64748b;">Supports PNG, JPG, JPEG up to 4MB</p>
                            </div>
                            <div id="imagePreviewBox" class="d-none position-relative d-inline-block">
                                <img id="imagePreview" src="" alt="preview" style="max-height: 150px; border-radius: 8px; border: 1px solid #cbd5e1;">
                                <button type="button" id="removeImageBtn" class="btn btn-danger btn-sm rounded-circle position-absolute" style="top: -10px; right: -10px; border-radius: 50%; padding: 4px 8px; font-size: 11px;"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="verifier-actions">
                        <button type="submit" id="verifyBtn" class="verifier-btn">
                            <span class="spinner" id="btnSpinner" style="display: none;"></span>
                            <span id="btnText">Verify Now</span>
                            <svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2.78609 0.636774C3.42696 1.41352 5.13873 1.95475 6.7051 2.30304C8.72196 2.75133 10.7973 2.8843 12.8309 2.61539C14.3541 2.41363 16.072 2.00892 17.0527 1.19981M14.9104 15.3317C14.2695 14.555 14.0633 12.7716 14.0189 11.1676C13.962 9.10235 14.2256 7.03943 14.8759 5.09401C15.3633 3.63687 16.087 2.02707 17.0676 1.21797M17.0602 1.2089L0.63638 14.7596" stroke="white" stroke-width="2" />
                            </svg>
                        </button>
                        <button type="button" id="clearBtn" class="verifier-btn-clear">Clear Input</button>
                    </div>
                </form>

                <!-- Processing Loader -->
                <div id="analysisLoader" class="analysis-loader" style="display: none;">
                    <div class="analysis-step" id="step1">
                        <i class="fa-solid fa-font"></i> Reading the text and checking how it is written...
                    </div>
                    <div class="analysis-step" id="step2">
                        <i class="fa-solid fa-link"></i> Looking for source links and known publishers...
                    </div>
                    <div class="analysis-step" id="step3">
                        <i class="fa-solid fa-scale-balanced"></i> Checking any factual claims against our records...
                    </div>
                    <div class="analysis-step" id="step4">
                        <i class="fa-solid fa-brain"></i> Running the trained AI model...
                    </div>
                </div>

                <!-- Verification Results Panel -->
                <div id="verifierResults" class="verifier-results" style="display: none;">
                    <div class="result-header-box">
                        <div class="score-circle-container">
                            <svg class="score-svg" viewBox="0 0 140 140">
                                <circle class="score-bg" cx="70" cy="70" r="60"></circle>
                                <circle class="score-fill" id="scoreFill" cx="70" cy="70" r="60"></circle>
                            </svg>
                            <div class="score-text">
                                <div class="score-val" id="scoreValue">0%</div>
                                <div class="score-lbl">Trust Rating</div>
                            </div>
                        </div>
                        <div class="verdict-info">
                            <span class="verdict-badge" id="verdictBadge">Calculating</span>
                            <h4 class="verdict-title" id="verdictTitle">Evaluating...</h4>
                            <p class="verdict-desc" id="verdictDesc">Please wait while the AI finishes the semantic parsing.</p>
                        </div>
                    </div>

                    <!-- Fact check evidence (only shown when we could check a real claim) -->
                    <div id="factCheckPanel" class="fact-check-panel" style="display: none;"></div>

                    <!-- Plain-language pattern breakdown -->
                    <div id="patternBreakdown" class="pattern-breakdown" style="display: none;"></div>

                    <div class="metrics-grid">
                        <!-- Style check card -->
                        <div class="metric-card">
                            <div class="metric-header">
                                <span class="metric-name">Writing Style</span>
                                <span class="metric-score" id="valStyle">0%</span>
                            </div>
                            <div class="metric-progress">
                                <div class="metric-progress-bar" id="barStyle"></div>
                            </div>
                            <p class="metric-desc">Looks at CAPITAL LETTERS, exclamation marks and clickbait wording. A higher score means calmer, more professional writing.</p>
                        </div>

                        <!-- Source audit card -->
                        <div class="metric-card">
                            <div class="metric-header">
                                <span class="metric-name">Sources &amp; Links</span>
                                <span class="metric-score" id="valSource">0%</span>
                            </div>
                            <div class="metric-progress">
                                <div class="metric-progress-bar" id="barSource"></div>
                            </div>
                            <p class="metric-desc">Checks whether the text links to a recognised news outlet, an unknown site, or gives no source at all.</p>
                        </div>

                        <!-- Fact check card -->
                        <div class="metric-card">
                            <div class="metric-header">
                                <span class="metric-name">Fact Check</span>
                                <span class="metric-score" id="valDatabase">Not checked</span>
                            </div>
                            <div class="metric-progress">
                                <div class="metric-progress-bar" id="barDatabase"></div>
                            </div>
                            <p class="metric-desc">Looks for claims we can actually verify, such as who holds a public office, and compares them with our fact database, Wikidata and published fact-checks.</p>
                        </div>

                        <!-- Model confidence card -->
                        <div class="metric-card">
                            <div class="metric-header">
                                <span class="metric-name">AI Model Confidence</span>
                                <span class="metric-score" id="valML">0%</span>
                            </div>
                            <div class="metric-progress">
                                <div class="metric-progress-bar" id="barML"></div>
                            </div>
                            <p class="metric-desc">How sure our trained model is about its own style-based reading of the text.</p>
                        </div>
                    </div>

                    <p id="resultDisclaimer" class="result-disclaimer" style="display: none;"></p>
                </div>
            </div>
        </div>
    </section>
    <!-- Verifier Widget Section End -->

    <!-- rts feature area start -->
    <section id="features" class="rts-feature-area area-6 rts-section-gap2">
        <div class="container-1340">
            <div class="section-inner">
                <div class="section-title-area center-style">
                    <h2 class="section-title rts-text-anime-style-1 text-transform-0">State-of-the-Art <br> News Auditing Tools</h2>
                </div>
                <div class="feature-area-inner mt--30">
                    <div class="row g-28">
                        <div class="col-lg-4 col-md-6">
                            <div class="feature-wrapper-6 wow fadeInRight" data-wow-delay="0.2s">
                                <h3 class="title">Linguistic & Style Check</h3>
                                <p class="desc">Exposes capitalized formatting, excessive exclamation marks, emotional hyperbole, and grammar anomalies.</p>
                                <div class="inner-icon">
                                    <div class="icon">
                                        <a href="#verifier" class="rts-btn btn-link">
                                            <svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M2.78609 0.636774C3.42696 1.41352 5.13873 1.95475 6.7051 2.30304C8.72196 2.75133 10.7973 2.8843 12.8309 2.61539C14.3541 2.41363 16.072 2.00892 17.0527 1.19981M14.9104 15.3317C14.2695 14.555 14.0633 12.7716 14.0189 11.1676C13.962 9.10235 14.2256 7.03943 14.8759 5.09401C15.3633 3.63687 16.087 2.02707 17.0676 1.21797M17.0602 1.2089L0.63638 14.7596" stroke="white" stroke-width="2" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="feature-wrapper-6 wow fadeInRight" data-wow-delay="0.4s">
                                <h3 class="title">Fact-Check Matches</h3>
                                <p class="desc">Cross-references claims, named entities, and topics against world fact archives in under 4 seconds.</p>
                                <div class="inner-icon">
                                    <div class="icon">
                                        <a href="#verifier" class="rts-btn btn-link">
                                            <svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M2.78609 0.636774C3.42696 1.41352 5.13873 1.95475 6.7051 2.30304C8.72196 2.75133 10.7973 2.8843 12.8309 2.61539C14.3541 2.41363 16.072 2.00892 17.0527 1.19981M14.9104 15.3317C14.2695 14.555 14.0633 12.7716 14.0189 11.1676C13.962 9.10235 14.2256 7.03943 14.8759 5.09401C15.3633 3.63687 16.087 2.02707 17.0676 1.21797M17.0602 1.2089L0.63638 14.7596" stroke="white" stroke-width="2" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="feature-wrapper-6 wow fadeInRight" data-wow-delay="0.6s">
                                <h3 class="title">Sentiment Bias NLP</h3>
                                <p class="desc">Grades text objectivity profile, identifying hostile adjectives and sensational vocabulary patterns.</p>
                                <div class="inner-icon">
                                    <div class="icon">
                                        <a href="#verifier" class="rts-btn btn-link">
                                            <svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M2.78609 0.636774C3.42696 1.41352 5.13873 1.95475 6.7051 2.30304C8.72196 2.75133 10.7973 2.8843 12.8309 2.61539C14.3541 2.41363 16.072 2.00892 17.0527 1.19981M14.9104 15.3317C14.2695 14.555 14.0633 12.7716 14.0189 11.1676C13.962 9.10235 14.2256 7.03943 14.8759 5.09401C15.3633 3.63687 16.087 2.02707 17.0676 1.21797M17.0602 1.2089L0.63638 14.7596" stroke="white" stroke-width="2" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="feature-wrapper-6 wow fadeInRight" data-wow-delay="0.8s">
                                <h3 class="title">Source Reputation Check</h3>
                                <p class="desc">Scans URL registers, domain registrations, safety warnings, and WHOIS publisher track records.</p>
                                <div class="inner-icon">
                                    <div class="icon">
                                        <a href="#verifier" class="rts-btn btn-link">
                                            <svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M2.78609 0.636774C3.42696 1.41352 5.13873 1.95475 6.7051 2.30304C8.72196 2.75133 10.7973 2.8843 12.8309 2.61539C14.3541 2.41363 16.072 2.00892 17.0527 1.19981M14.9104 15.3317C14.2695 14.555 14.0633 12.7716 14.0189 11.1676C13.962 9.10235 14.2256 7.03943 14.8759 5.09401C15.3633 3.63687 16.087 2.02707 17.0676 1.21797M17.0602 1.2089L0.63638 14.7596" stroke="white" stroke-width="2" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="feature-wrapper-6 wow fadeInRight" data-wow-delay="1.0s">
                                <h3 class="title">Your Checks, Saved for You</h3>
                                <p class="desc">Every check is sent over a secure connection and stored so you can revisit it in your history. We never sell your data or share it with advertisers.</p>
                                <div class="inner-icon">
                                    <div class="icon">
                                        <a href="#verifier" class="rts-btn btn-link">
                                            <svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M2.78609 0.636774C3.42696 1.41352 5.13873 1.95475 6.7051 2.30304C8.72196 2.75133 10.7973 2.8843 12.8309 2.61539C14.3541 2.41363 16.072 2.00892 17.0527 1.19981M14.9104 15.3317C14.2695 14.555 14.0633 12.7716 14.0189 11.1676C13.962 9.10235 14.2256 7.03943 14.8759 5.09401C15.3633 3.63687 16.087 2.02707 17.0676 1.21797M17.0602 1.2089L0.63638 14.7596" stroke="white" stroke-width="2" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="feature-wrapper-6 wow fadeInRight" data-wow-delay="1.2s">
                                <h3 class="title">API & Extensions</h3>
                                <p class="desc">Run background audits as you surf social feeds with browser plug-ins or integrate with newsroom platforms.</p>
                                <div class="inner-icon">
                                    <div class="icon">
                                        <a href="#verifier" class="rts-btn btn-link">
                                            <svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M2.78609 0.636774C3.42696 1.41352 5.13873 1.95475 6.7051 2.30304C8.72196 2.75133 10.7973 2.8843 12.8309 2.61539C14.3541 2.41363 16.072 2.00892 17.0527 1.19981M14.9104 15.3317C14.2695 14.555 14.0633 12.7716 14.0189 11.1676C13.962 9.10235 14.2256 7.03943 14.8759 5.09401C15.3633 3.63687 16.087 2.02707 17.0676 1.21797M17.0602 1.2089L0.63638 14.7596" stroke="white" stroke-width="2" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- rts feature area end -->

    <!-- rts why choose area start -->
    <section class="rts-why-choose-area rts-section-gap2">
        <div class="container-1340">
            <div class="section-inner">
                <div class="section-title-area center-style">
                    <h2 class="section-title rts-text-anime-style-1 text-transform-0">Why Millions Trust VeriFact AI</h2>
                </div>
                <div class="why-choose-area-inner mt--50">
                    <div class="row g-28">
                        <div class="col-lg-7 col-md-6">
                            <div class="why-choose-wrapper shape-one wow scaleIn" data-wow-delay="0.2s">
                                <div class="icon">
                                    <img src="{{ asset('assets/images/why-choose/telemedicine.svg') }}" alt="icon">
                                </div>
                                <h3 class="title cw">Instant Credibility Grades</h3>
                                <p class="desc cw">Get clear visual status indicators, exact truth percentages, and a structural language diagnosis in under 4 seconds.</p>
                                <div class="inner-shape">
                                    <div class="inner"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5 col-md-6">
                            <div class="why-choose-wrapper shape-two wow scaleIn" data-wow-delay="0.4s">
                                <div class="icon">
                                    <img src="{{ asset('assets/images/why-choose/medical-record.svg') }}" alt="icon">
                                </div>
                                <h3 class="title cw">100% Unbiased Auditing</h3>
                                <p class="desc cw">Our analyzer executes checks based strictly on factual metrics and vocabulary styling, remaining totally neutral.</p>
                                <div class="inner-shape">
                                    <div class="inner"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="why-choose-wrapper shape-three wow scaleIn" data-wow-delay="0.6s">
                                <div class="icon">
                                    <img src="{{ asset('assets/images/why-choose/wishlist.svg') }}" alt="icon">
                                </div>
                                <h3 class="title cw">Continuous Database Synced</h3>
                                <p class="desc cw">We constantly sync with academic archives and global fact-checking agencies to keep our reference patterns updated.</p>
                                <div class="inner-shape">
                                    <div class="inner"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- rts why choose area end -->

    <!-- rts services area start -->
    <section id="services" class="rts-services-area-two rts-section-gap2">
        <div class="container-1340">
            <div class="section-title-area">
                <h2 class="section-title cw rts-text-anime-style-1 text-transform-0">
                    Fact-Checking & Verification <br> Solutions for Media Integrity
                </h2>
            </div>
            <div class="services-area-inner mt--50 wow fadeInUp" data-wow-delay="0.2s">
                <div class="row gy-5 gy-lg-0">
                    <div class="col-lg-7">
                        <div class="service-nav-wrappper">
                            <div class="nav nav-tabs service-tabs" id="servicesTab" role="tablist">
                                <button class="nav-link active" id="service-one-tab" data-bs-toggle="tab" data-bs-target="#service-one" type="button" role="tab" aria-controls="service-one" aria-selected="true">
                                    <span class="left">
                                        <span class="number">01</span>
                                        <span class="content">
                                            <span class="title">Social Media Feed Scanner</span>
                                            <span class="desc">Scan and audit viral social media posts, comments, and claims for coordinated disinformation campaigns and fake narratives.</span>
                                        </span>
                                    </span>
                                    <span class="right">
                                        <span class="rts-link-btn">
                                            <svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M2.78609 0.636774C3.42696 1.41352 5.13873 1.95475 6.7051 2.30304C8.72196 2.75133 10.7973 2.8843 12.8309 2.61539C14.3541 2.41363 16.072 2.00892 17.0527 1.19981M14.9104 15.3317C14.2695 14.555 14.0633 12.7716 14.0189 11.1676C13.962 9.10235 14.2256 7.03943 14.8759 5.09401C15.3633 3.63687 16.087 2.02707 17.0676 1.21797M17.0602 1.2089L0.63638 14.7596" stroke="white" stroke-width="2" />
                                            </svg>
                                        </span>
                                    </span>
                                </button>
                                <button class="nav-link" id="service-two-tab" data-bs-toggle="tab" data-bs-target="#service-two" type="button" role="tab" aria-controls="service-two" aria-selected="false">
                                    <span class="left">
                                        <span class="number">02</span>
                                        <span class="content">
                                            <span class="title">Newsroom & Journalism SDK</span>
                                            <span class="desc">Empower reporters to run instant cross-reference audits and domain credibility reviews before stories go to press.</span>
                                        </span>
                                    </span>
                                    <span class="right">
                                        <span class="rts-link-btn">
                                            <svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M2.78609 0.636774C3.42696 1.41352 5.13873 1.95475 6.7051 2.30304C8.72196 2.75133 10.7973 2.8843 12.8309 2.61539C14.3541 2.41363 16.072 2.00892 17.0527 1.19981M14.9104 15.3317C14.2695 14.555 14.0633 12.7716 14.0189 11.1676C13.962 9.10235 14.2256 7.03943 14.8759 5.09401C15.3633 3.63687 16.087 2.02707 17.0676 1.21797M17.0602 1.2089L0.63638 14.7596" stroke="white" stroke-width="2" />
                                            </svg>
                                        </span>
                                    </span>
                                </button>
                                <button class="nav-link" id="service-three-tab" data-bs-toggle="tab" data-bs-target="#service-three" type="button" role="tab" aria-controls="service-three" aria-selected="false">
                                    <span class="left">
                                        <span class="number">03</span>
                                        <span class="content">
                                            <span class="title">Corporate Brand Safety</span>
                                            <span class="desc">Identify bad-faith review spam, disinformation smear campaigns, and toxic fabricated claims targeting your business reputation.</span>
                                        </span>
                                    </span>
                                    <span class="right">
                                        <span class="rts-link-btn">
                                            <svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M2.78609 0.636774C3.42696 1.41352 5.13873 1.95475 6.7051 2.30304C8.72196 2.75133 10.7973 2.8843 12.8309 2.61539C14.3541 2.41363 16.072 2.00892 17.0527 1.19981M14.9104 15.3317C14.2695 14.555 14.0633 12.7716 14.0189 11.1676C13.962 9.10235 14.2256 7.03943 14.8759 5.09401C15.3633 3.63687 16.087 2.02707 17.0676 1.21797M17.0602 1.2089L0.63638 14.7596" stroke="white" stroke-width="2" />
                                            </svg>
                                        </span>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="service-tab-content-wrapper" id="servicesTabContent">
                            <div class="tab-pane fade show active" id="service-one" role="tabpanel" aria-labelledby="service-one-tab" tabindex="0">
                                <div class="rts-services-images-area">
                                    <div class="inner-shape">
                                        <div class="inner"></div>
                                    </div>
                                    <div class="image-area">
                                        <img src="{{ asset('assets/images/service/05.webp') }}" alt="Social Media Scanner">
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="service-two" role="tabpanel" aria-labelledby="service-two-tab" tabindex="0">
                                <div class="rts-services-images-area">
                                    <div class="inner-shape">
                                        <div class="inner"></div>
                                    </div>
                                    <div class="image-area">
                                        <img src="{{ asset('assets/images/service/06.webp') }}" alt="Newsroom SDK">
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="service-three" role="tabpanel" aria-labelledby="service-three-tab" tabindex="0">
                                <div class="rts-services-images-area">
                                    <div class="inner-shape">
                                        <div class="inner"></div>
                                    </div>
                                    <div class="image-area">
                                        <img src="{{ asset('assets/images/service/04.webp') }}" alt="Brand Safety Audit">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- rts services area end -->

    <!-- rts testimonials area start -->
    <section class="rts-testimonials-area area-6 rts-section-gap2">
        <div class="container-1340">
            <div class="section-title-area center-style">
                <h2 class="section-title rts-text-anime-style-1 text-transform-0">What Truth-Seekers Are Saying</h2>
                <p class="desc wow fadeInUp" data-wow-delay="0.2s">VeriFact AI is trusted by researchers, journalists, and everyday readers to maintain objective information standards.</p>
            </div>
            <div class="section-inner mt--60 wow fadeInUp" data-wow-delay="0.4s">
                <div class="swiper testimonialsSlider3">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <div class="testimonials-slide-wrapper slide-one">
                                <div class="inner-shape">
                                    <div class="inner"></div>
                                </div>
                                <div class="icon">
                                    <img src="{{ asset('assets/images/testimonials/testi-logo-03.svg') }}" alt="icon">
                                </div>
                                <p class="desc">
                                    “ VeriFact AI has completely changed how I read news on social media. I check every viral headline before sharing, and the breakdown helps me spot clickbait immediately! ”
                                </p>
                                <div class="author-details">
                                    <div class="author-thumb">
                                        <img src="{{ asset('assets/images/testimonials/author-13.webp') }}" width="72" alt="authors">
                                    </div>
                                    <div class="author-info">
                                        <h3 class="author-name">Tanvir Jamil</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="testimonials-slide-wrapper slide-two">
                                <p class="desc">
                                    “ As a freelance journalist, checking source claims and domain registrations is critical. This tool automates the process and saves me hours of manual cross-referencing. ”
                                </p>
                                <div class="author-details">
                                    <div class="author-thumb">
                                        <img src="{{ asset('assets/images/testimonials/author-14.webp') }}" width="72" alt="authors">
                                    </div>
                                    <div class="author-info">
                                        <h3 class="author-name">Sarah Khan</h3>
                                    </div>
                                </div>
                                <div class="icon">
                                    <img src="{{ asset('assets/images/testimonials/testi-logo-04.svg') }}" alt="icon">
                                </div>
                                <div class="inner-shape">
                                    <div class="inner"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="navigation-button-area">
                    <div class="swiper-btn swiper-btn-prev">
                        <svg width="22" height="20" viewBox="0 0 22 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.6468 0C10.6468 1.007 9.67092 2.51387 8.68436 3.77938C7.41395 5.40873 5.89774 6.83209 4.15804 7.91883C2.85472 8.73259 1.27203 9.51374 0.000669479 9.51374M10.6468 19.051C10.6468 18.044 9.67092 16.5371 8.68436 15.2717C7.41395 13.6424 5.89774 12.2189 4.15804 11.1322C2.85472 10.3184 1.27203 9.53727 0.000669479 9.53727M0.000669479 9.52551H21.293" stroke="#F54329" stroke-width="2" />
                        </svg>
                    </div>
                    <div class="swiper-btn swiper-btn-next">
                        <svg width="22" height="20" viewBox="0 0 22 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.6461 0C10.6461 1.007 11.6221 2.51387 12.6086 3.77938C13.879 5.40873 15.3952 6.83209 17.1349 7.91883C18.4383 8.73259 20.0209 9.51374 21.2923 9.51374M10.6461 19.051C10.6461 18.044 11.6221 16.5371 12.6086 15.2717C13.879 13.6424 15.3952 12.2189 17.1349 11.1322C18.4383 10.3184 20.0209 9.53727 21.2923 9.53727M21.2923 9.52551H0" stroke="#F54329" stroke-width="2" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- rts testimonials area end -->

    <!-- rts pricing area start -->
    <section id="pricing" class="rts-pricing-area area-6 rts-section-gap2">
        <div class="container-1340">
            <div class="section-title-area center-style">
                <h2 class="section-title rts-text-anime-style-1 text-transform-0">Flexible Plans for Every User</h2>
                <p class="desc wow fadeInUp" data-wow-delay="0.2s">Every verification feature on this site is currently free while VeriFact AI is in development. The paid tiers below show where the project is heading — no payments are being taken yet.</p>
            </div>
            <div class="section-inner mt--60">
                <div class="row g-28">
                    <div class="col-lg-4 col-md-6">
                        <div class="pricing-wrapper6 wow fadeInRight" data-wow-delay="0.2s">
                            <div class="pricing-header">
                                <span class="tag">Free</span>
                                <h3 class="price">$0 <span>/ Per Month</span></h3>
                                <p class="desc">Ideal for casual news readers and social media surfers</p>
                            </div>
                            <div class="pricing-body">
                                <ul>
                                    <li>
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="10" cy="10" r="10" fill="#F54329" />
                                            <path d="M15.0429 6.23787C14.9682 6.1625 14.8792 6.10267 14.7812 6.06185C14.6832 6.02102 14.5781 6 14.472 6C14.3658 6 14.2607 6.02102 14.1627 6.06185C14.0647 6.10267 13.9758 6.1625 13.901 6.23787L7.90986 12.2371L5.39278 9.71193C5.31516 9.63695 5.22353 9.57799 5.12312 9.53842C5.02271 9.49886 4.9155 9.47945 4.80759 9.48132C4.69969 9.48318 4.59321 9.50629 4.49423 9.5493C4.39525 9.59232 4.30572 9.65442 4.23074 9.73204C4.15576 9.80966 4.0968 9.90129 4.05723 10.0017C4.01766 10.1021 3.99826 10.2093 4.00012 10.3172C4.00199 10.4251 4.02509 10.5316 4.06811 10.6306C4.11113 10.7296 4.17322 10.8191 4.25084 10.8941L7.33889 13.9821C7.41365 14.0575 7.50259 14.1173 7.60059 14.1582C7.69859 14.199 7.8037 14.22 7.90986 14.22C8.01602 14.22 8.12113 14.199 8.21913 14.1582C8.31712 14.1173 8.40607 14.0575 8.48083 13.9821L15.0429 7.42002C15.1246 7.34471 15.1897 7.25332 15.2343 7.15159C15.2788 7.04986 15.3018 6.94001 15.3018 6.82895C15.3018 6.71789 15.2788 6.60803 15.2343 6.50631C15.1897 6.40458 15.1246 6.31318 15.0429 6.23787Z" fill="white" />
                                        </svg>
                                        10 free verification searches / day
                                    </li>
                                    <li>
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="10" cy="10" r="10" fill="#F54329" />
                                            <path d="M15.0429 6.23787C14.9682 6.1625 14.8792 6.10267 14.7812 6.06185C14.6832 6.02102 14.5781 6 14.472 6C14.3658 6 14.2607 6.02102 14.1627 6.06185C14.0647 6.10267 13.9758 6.1625 13.901 6.23787L7.90986 12.2371L5.39278 9.71193C5.31516 9.63695 5.22353 9.57799 5.12312 9.53842C5.02271 9.49886 4.9155 9.47945 4.80759 9.48132C4.69969 9.48318 4.59321 9.50629 4.49423 9.5493C4.39525 9.59232 4.30572 9.65442 4.23074 9.73204C4.15576 9.80966 4.0968 9.90129 4.05723 10.0017C4.01766 10.1021 3.99826 10.2093 4.00012 10.3172C4.00199 10.4251 4.02509 10.5316 4.06811 10.6306C4.11113 10.7296 4.17322 10.8191 4.25084 10.8941L7.33889 13.9821C7.41365 14.0575 7.50259 14.1173 7.60059 14.1582C7.69859 14.199 7.8037 14.22 7.90986 14.22C8.01602 14.22 8.12113 14.199 8.21913 14.1582C8.31712 14.1173 8.40607 14.0575 8.48083 13.9821L15.0429 7.42002C15.1246 7.34471 15.1897 7.25332 15.2343 7.15159C15.2788 7.04986 15.3018 6.94001 15.3018 6.82895C15.3018 6.71789 15.2788 6.60803 15.2343 6.50631C15.1897 6.40458 15.1246 6.31318 15.0429 6.23787Z" fill="white" />
                                        </svg>
                                        Standard linguistic style checks
                                    </li>
                                    <li>
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="10" cy="10" r="10" fill="#F54329" />
                                            <path d="M15.0429 6.23787C14.9682 6.1625 14.8792 6.10267 14.7812 6.06185C14.6832 6.02102 14.5781 6 14.472 6C14.3658 6 14.2607 6.02102 14.1627 6.06185C14.0647 6.10267 13.9758 6.1625 13.901 6.23787L7.90986 12.2371L5.39278 9.71193C5.31516 9.63695 5.22353 9.57799 5.12312 9.53842C5.02271 9.49886 4.9155 9.47945 4.80759 9.48132C4.69969 9.48318 4.59321 9.50629 4.49423 9.5493C4.39525 9.59232 4.30572 9.65442 4.23074 9.73204C4.15576 9.80966 4.0968 9.90129 4.05723 10.0017C4.01766 10.1021 3.99826 10.2093 4.00012 10.3172C4.00199 10.4251 4.02509 10.5316 4.06811 10.6306C4.11113 10.7296 4.17322 10.8191 4.25084 10.8941L7.33889 13.9821C7.41365 14.0575 7.50259 14.1173 7.60059 14.1582C7.69859 14.199 7.8037 14.22 7.90986 14.22C8.01602 14.22 8.12113 14.199 8.21913 14.1582C8.31712 14.1173 8.40607 14.0575 8.48083 13.9821L15.0429 7.42002C15.1246 7.34471 15.1897 7.25332 15.2343 7.15159C15.2788 7.04986 15.3018 6.94001 15.3018 6.82895C15.3018 6.71789 15.2788 6.60803 15.2343 6.50631C15.1897 6.40458 15.1246 6.31318 15.0429 6.23787Z" fill="white" />
                                        </svg>
                                        Browser extension access
                                    </li>
                                </ul>
                                <a href="{{ route('register') }}" class="rts-btn btn-primary">Get Started</a>
                            </div>
                            <div class="inner-shape">
                                <div class="icon">
                                    <a href="{{ route('register') }}" class="rts-btn">
                                        <svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M2.78609 0.636774C3.42696 1.41352 5.13873 1.95475 6.7051 2.30304C8.72196 2.75133 10.7973 2.8843 12.8309 2.61539C14.3541 2.41363 16.072 2.00892 17.0527 1.19981M14.9104 15.3317C14.2695 14.555 14.0633 12.7716 14.0189 11.1676C13.962 9.10235 14.2256 7.03943 14.8759 5.09401C15.3633 3.63687 16.087 2.02707 17.0676 1.21797M17.0602 1.2089L0.63638 14.7596" stroke="white" stroke-width="2" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="pricing-wrapper6 wow fadeInRight" data-wow-delay="0.4s">
                            <div class="pricing-header">
                                <span class="tag">Pro</span>
                                <h3 class="price">$19.99 <span>/ Per Month</span></h3>
                                <p class="desc">For professionals, journalists, and power users</p>
                            </div>
                            <div class="pricing-body">
                                <ul>
                                    <li>
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="10" cy="10" r="10" fill="#F54329" />
                                            <path d="M15.0429 6.23787C14.9682 6.1625 14.8792 6.10267 14.7812 6.06185C14.6832 6.02102 14.5781 6 14.472 6C14.3658 6 14.2607 6.02102 14.1627 6.06185C14.0647 6.10267 13.9758 6.1625 13.901 6.23787L7.90986 12.2371L5.39278 9.71193C5.31516 9.63695 5.22353 9.57799 5.12312 9.53842C5.02271 9.49886 4.9155 9.47945 4.80759 9.48132C4.69969 9.48318 4.59321 9.50629 4.49423 9.5493C4.39525 9.59232 4.30572 9.65442 4.23074 9.73204C4.15576 9.80966 4.0968 9.90129 4.05723 10.0017C4.01766 10.1021 3.99826 10.2093 4.00012 10.3172C4.00199 10.4251 4.02509 10.5316 4.06811 10.6306C4.11113 10.7296 4.17322 10.8191 4.25084 10.8941L7.33889 13.9821C7.41365 14.0575 7.50259 14.1173 7.60059 14.1582C7.69859 14.199 7.8037 14.22 7.90986 14.22C8.01602 14.22 8.12113 14.199 8.21913 14.1582C8.31712 14.1173 8.40607 14.0575 8.48083 13.9821L15.0429 7.42002C15.1246 7.34471 15.1897 7.25332 15.2343 7.15159C15.2788 7.04986 15.3018 6.94001 15.3018 6.82895C15.3018 6.71789 15.2788 6.60803 15.2343 6.50631C15.1897 6.40458 15.1246 6.31318 15.0429 6.23787Z" fill="white" />
                                        </svg>
                                        Unlimited manual credibility searches
                                    </li>
                                    <li>
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="10" cy="10" r="10" fill="#F54329" />
                                            <path d="M15.0429 6.23787C14.9682 6.1625 14.8792 6.10267 14.7812 6.06185C14.6832 6.02102 14.5781 6 14.472 6C14.3658 6 14.2607 6.02102 14.1627 6.06185C14.0647 6.10267 13.9758 6.1625 13.901 6.23787L7.90986 12.2371L5.39278 9.71193C5.31516 9.63695 5.22353 9.57799 5.12312 9.53842C5.02271 9.49886 4.9155 9.47945 4.80759 9.48132C4.69969 9.48318 4.59321 9.50629 4.49423 9.5493C4.39525 9.59232 4.30572 9.65442 4.23074 9.73204C4.15576 9.80966 4.0968 9.90129 4.05723 10.0017C4.01766 10.1021 3.99826 10.2093 4.00012 10.3172C4.00199 10.4251 4.02509 10.5316 4.06811 10.6306C4.11113 10.7296 4.17322 10.8191 4.25084 10.8941L7.33889 13.9821C7.41365 14.0575 7.50259 14.1173 7.60059 14.1582C7.69859 14.199 7.8037 14.22 7.90986 14.22C8.01602 14.22 8.12113 14.199 8.21913 14.1582C8.31712 14.1173 8.40607 14.0575 8.48083 13.9821L15.0429 7.42002C15.1246 7.34471 15.1897 7.25332 15.2343 7.15159C15.2788 7.04986 15.3018 6.94001 15.3018 6.82895C15.3018 6.71789 15.2788 6.60803 15.2343 6.50631C15.1897 6.40458 15.1246 6.31318 15.0429 6.23787Z" fill="white" />
                                        </svg>
                                        Detailed linguistic bias diagnostics
                                    </li>
                                    <li>
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="10" cy="10" r="10" fill="#F54329" />
                                            <path d="M15.0429 6.23787C14.9682 6.1625 14.8792 6.10267 14.7812 6.06185C14.6832 6.02102 14.5781 6 14.472 6C14.3658 6 14.2607 6.02102 14.1627 6.06185C14.0647 6.10267 13.9758 6.1625 13.901 6.23787L7.90986 12.2371L5.39278 9.71193C5.31516 9.63695 5.22353 9.57799 5.12312 9.53842C5.02271 9.49886 4.9155 9.47945 4.80759 9.48132C4.69969 9.48318 4.59321 9.50629 4.49423 9.5493C4.39525 9.59232 4.30572 9.65442 4.23074 9.73204C4.15576 9.80966 4.0968 9.90129 4.05723 10.0017C4.01766 10.1021 3.99826 10.2093 4.00012 10.3172C4.00199 10.4251 4.02509 10.5316 4.06811 10.6306C4.11113 10.7296 4.17322 10.8191 4.25084 10.8941L7.33889 13.9821C7.41365 14.0575 7.50259 14.1173 7.60059 14.1582C7.69859 14.199 7.8037 14.22 7.90986 14.22C8.01602 14.22 8.12113 14.199 8.21913 14.1582C8.31712 14.1173 8.40607 14.0575 8.48083 13.9821L15.0429 7.42002C15.1246 7.34471 15.1897 7.25332 15.2343 7.15159C15.2788 7.04986 15.3018 6.94001 15.3018 6.82895C15.3018 6.71789 15.2788 6.60803 15.2343 6.50631C15.1897 6.40458 15.1246 6.31318 15.0429 6.23787Z" fill="white" />
                                        </svg>
                                        Standard API integration access
                                    </li>
                                </ul>
                                <span class="rts-btn btn-primary disabled" aria-disabled="true">Coming Soon</span>
                            </div>
                            <div class="inner-shape">
                                <div class="icon">
                                    <a href="#pricing" class="rts-btn">
                                        <svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M2.78609 0.636774C3.42696 1.41352 5.13873 1.95475 6.7051 2.30304C8.72196 2.75133 10.7973 2.8843 12.8309 2.61539C14.3541 2.41363 16.072 2.00892 17.0527 1.19981M14.9104 15.3317C14.2695 14.555 14.0633 12.7716 14.0189 11.1676C13.962 9.10235 14.2256 7.03943 14.8759 5.09401C15.3633 3.63687 16.087 2.02707 17.0676 1.21797M17.0602 1.2089L0.63638 14.7596" stroke="white" stroke-width="2" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="pricing-wrapper6 last wow fadeInRight" data-wow-delay="0.6s">
                            <div class="pricing-header">
                                <span class="tag">Enterprise</span>
                                <h3 class="price">$49.99 <span>/ Per Month</span></h3>
                                <p class="desc">For newsrooms, agencies, and brand managers</p>
                            </div>
                            <div class="pricing-body">
                                <ul>
                                    <li>
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="10" cy="10" r="10" fill="#F54329" />
                                            <path d="M15.0429 6.23787C14.9682 6.1625 14.8792 6.10267 14.7812 6.06185C14.6832 6.02102 14.5781 6 14.472 6C14.3658 6 14.2607 6.02102 14.1627 6.06185C14.0647 6.10267 13.9758 6.1625 13.901 6.23787L7.90986 12.2371L5.39278 9.71193C5.31516 9.63695 5.22353 9.57799 5.12312 9.53842C5.02271 9.49886 4.9155 9.47945 4.80759 9.48132C4.69969 9.48318 4.59321 9.50629 4.49423 9.5493C4.39525 9.59232 4.30572 9.65442 4.23074 9.73204C4.15576 9.80966 4.0968 9.90129 4.05723 10.0017C4.01766 10.1021 3.99826 10.2093 4.00012 10.3172C4.00199 10.4251 4.02509 10.5316 4.06811 10.6306C4.11113 10.7296 4.17322 10.8191 4.25084 10.8941L7.33889 13.9821C7.41365 14.0575 7.50259 14.1173 7.60059 14.1582C7.69859 14.199 7.8037 14.22 7.90986 14.22C8.01602 14.22 8.12113 14.199 8.21913 14.1582C8.31712 14.1173 8.40607 14.0575 8.48083 13.9821L15.0429 7.42002C15.1246 7.34471 15.1897 7.25332 15.2343 7.15159C15.2788 7.04986 15.3018 6.94001 15.3018 6.82895C15.3018 6.71789 15.2788 6.60803 15.2343 6.50631C15.1897 6.40458 15.1246 6.31318 15.0429 6.23787Z" fill="white" />
                                        </svg>
                                        All Premium & Pro features included
                                    </li>
                                    <li>
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="10" cy="10" r="10" fill="#F54329" />
                                            <path d="M15.0429 6.23787C14.9682 6.1625 14.8792 6.10267 14.7812 6.06185C14.6832 6.02102 14.5781 6 14.472 6C14.3658 6 14.2607 6.02102 14.1627 6.06185C14.0647 6.10267 13.9758 6.1625 13.901 6.23787L7.90986 12.2371L5.39278 9.71193C5.31516 9.63695 5.22353 9.57799 5.12312 9.53842C5.02271 9.49886 4.9155 9.47945 4.80759 9.48132C4.69969 9.48318 4.59321 9.50629 4.49423 9.5493C4.39525 9.59232 4.30572 9.65442 4.23074 9.73204C4.15576 9.80966 4.0968 9.90129 4.05723 10.0017C4.01766 10.1021 3.99826 10.2093 4.00012 10.3172C4.00199 10.4251 4.02509 10.5316 4.06811 10.6306C4.11113 10.7296 4.17322 10.8191 4.25084 10.8941L7.33889 13.9821C7.41365 14.0575 7.50259 14.1173 7.60059 14.1582C7.69859 14.199 7.8037 14.22 7.90986 14.22C8.01602 14.22 8.12113 14.199 8.21913 14.1582C8.31712 14.1173 8.40607 14.0575 8.48083 13.9821L15.0429 7.42002C15.1246 7.34471 15.1897 7.25332 15.2343 7.15159C15.2788 7.04986 15.3018 6.94001 15.3018 6.82895C15.3018 6.71789 15.2788 6.60803 15.2343 6.50631C15.1897 6.40458 15.1246 6.31318 15.0429 6.23787Z" fill="white" />
                                        </svg>
                                        Unlimited developer API queries
                                    </li>
                                    <li>
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="10" cy="10" r="10" fill="#F54329" />
                                            <path d="M15.0429 6.23787C14.9682 6.1625 14.8792 6.10267 14.7812 6.06185C14.6832 6.02102 14.5781 6 14.472 6C14.3658 6 14.2607 6.02102 14.1627 6.06185C14.0647 6.10267 13.9758 6.1625 13.901 6.23787L7.90986 12.2371L5.39278 9.71193C5.31516 9.63695 5.22353 9.57799 5.12312 9.53842C5.02271 9.49886 4.9155 9.47945 4.80759 9.48132C4.69969 9.48318 4.59321 9.50629 4.49423 9.5493C4.39525 9.59232 4.30572 9.65442 4.23074 9.73204C4.15576 9.80966 4.0968 9.90129 4.05723 10.0017C4.01766 10.1021 3.99826 10.2093 4.00012 10.3172C4.00199 10.4251 4.02509 10.5316 4.06811 10.6306C4.11113 10.7296 4.17322 10.8191 4.25084 10.8941L7.33889 13.9821C7.41365 14.0575 7.50259 14.1173 7.60059 14.1582C7.69859 14.199 7.8037 14.22 7.90986 14.22C8.01602 14.22 8.12113 14.199 8.21913 14.1582C8.31712 14.1173 8.40607 14.0575 8.48083 13.9821L15.0429 7.42002C15.1246 7.34471 15.1897 7.25332 15.2343 7.15159C15.2788 7.04986 15.3018 6.94001 15.3018 6.82895C15.3018 6.71789 15.2788 6.60803 15.2343 6.50631C15.1897 6.40458 15.1246 6.31318 15.0429 6.23787Z" fill="white" />
                                        </svg>
                                        Custom training dataset upload
                                    </li>
                                    <li>
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="10" cy="10" r="10" fill="#F54329" />
                                            <path d="M15.0429 6.23787C14.9682 6.1625 14.8792 6.10267 14.7812 6.06185C14.6832 6.02102 14.5781 6 14.472 6C14.3658 6 14.2607 6.02102 14.1627 6.06185C14.0647 6.10267 13.9758 6.1625 13.901 6.23787L7.90986 12.2371L5.39278 9.71193C5.31516 9.63695 5.22353 9.57799 5.12312 9.53842C5.02271 9.49886 4.9155 9.47945 4.80759 9.48132C4.69969 9.48318 4.59321 9.50629 4.49423 9.5493C4.39525 9.59232 4.30572 9.65442 4.23074 9.73204C4.15576 9.80966 4.0968 9.90129 4.05723 10.0017C4.01766 10.1021 3.99826 10.2093 4.00012 10.3172C4.00199 10.4251 4.02509 10.5316 4.06811 10.6306C4.11113 10.7296 4.17322 10.8191 4.25084 10.8941L7.33889 13.9821C7.41365 14.0575 7.50259 14.1173 7.60059 14.1582C7.69859 14.199 7.8037 14.22 7.90986 14.22C8.01602 14.22 8.12113 14.199 8.21913 14.1582C8.31712 14.1173 8.40607 14.0575 8.48083 13.9821L15.0429 7.42002C15.1246 7.34471 15.1897 7.25332 15.2343 7.15159C15.2788 7.04986 15.3018 6.94001 15.3018 6.82895C15.3018 6.71789 15.2788 6.60803 15.2343 6.50631C15.1897 6.40458 15.1246 6.31318 15.0429 6.23787Z" fill="white" />
                                        </svg>
                                        Shared team dashboard analytics
                                    </li>
                                    <li>
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="10" cy="10" r="10" fill="#F54329" />
                                            <path d="M15.0429 6.23787C14.9682 6.1625 14.8792 6.10267 14.7812 6.06185C14.6832 6.02102 14.5781 6 14.472 6C14.3658 6 14.2607 6.02102 14.1627 6.06185C14.0647 6.10267 13.9758 6.1625 13.901 6.23787L7.90986 12.2371L5.39278 9.71193C5.31516 9.63695 5.22353 9.57799 5.12312 9.53842C5.02271 9.49886 4.9155 9.47945 4.80759 9.48132C4.69969 9.48318 4.59321 9.50629 4.49423 9.5493C4.39525 9.59232 4.30572 9.65442 4.23074 9.73204C4.15576 9.80966 4.0968 9.90129 4.05723 10.0017C4.01766 10.1021 3.99826 10.2093 4.00012 10.3172C4.00199 10.4251 4.02509 10.5316 4.06811 10.6306C4.11113 10.7296 4.17322 10.8191 4.25084 10.8941L7.33889 13.9821C7.41365 14.0575 7.50259 14.1173 7.60059 14.1582C7.69859 14.199 7.8037 14.22 7.90986 14.22C8.01602 14.22 8.12113 14.199 8.21913 14.1582C8.31712 14.1173 8.40607 14.0575 8.48083 13.9821L15.0429 7.42002C15.1246 7.34471 15.1897 7.25332 15.2343 7.15159C15.2788 7.04986 15.3018 6.94001 15.3018 6.82895C15.3018 6.71789 15.2788 6.60803 15.2343 6.50631C15.1897 6.40458 15.1246 6.31318 15.0429 6.23787Z" fill="white" />
                                        </svg>
                                        24/7 dedicated support desk
                                    </li>
                                </ul>
                                <a href="{{ route('contact') }}" class="rts-btn btn-primary">Contact Us</a>
                            </div>
                            <div class="inner-shape">
                                <div class="icon">
                                    <a href="{{ route('contact') }}" class="rts-btn">
                                        <svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M2.78609 0.636774C3.42696 1.41352 5.13873 1.95475 6.7051 2.30304C8.72196 2.75133 10.7973 2.8843 12.8309 2.61539C14.3541 2.41363 16.072 2.00892 17.0527 1.19981M14.9104 15.3317C14.2695 14.555 14.0633 12.7716 14.0189 11.1676C13.962 9.10235 14.2256 7.03943 14.8759 5.09401C15.3633 3.63687 16.087 2.02707 17.0676 1.21797M17.0602 1.2089L0.63638 14.7596" stroke="white" stroke-width="2" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- rts pricing area end -->

    <!-- team area start -->
    <section id="team" class="rts-feature-area area-6 rts-section-gap2">
        <div class="container-1340">
            <div class="section-title-area center-style">
                <h2 class="section-title rts-text-anime-style-1 text-transform-0">Built By</h2>
                <p class="desc wow fadeInUp" data-wow-delay="0.2s">VeriFact AI is a BS Computer Science Final Year Project built at the University of Central Punjab, Lahore.</p>
            </div>
            <div class="section-inner mt--50">
                <div class="row g-28 justify-content-center">
                    @foreach ([
                        ['name' => 'Maaz Naveed', 'role' => 'Machine Learning & Backend', 'initials' => 'MN'],
                        ['name' => 'Jazil Mehmood', 'role' => 'Frontend & User Experience', 'initials' => 'JM'],
                        ['name' => 'Muhammad Abrar', 'role' => 'Data, Testing & Documentation', 'initials' => 'MA'],
                    ] as $index => $member)
                        <div class="col-lg-4 col-md-6">
                            <div class="team-card wow fadeInUp" data-wow-delay="0.{{ $index * 2 + 2 }}s">
                                <div class="team-avatar">{{ $member['initials'] }}</div>
                                <h3 class="team-name">{{ $member['name'] }}</h3>
                                <p class="team-role">{{ $member['role'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="team-meta wow fadeInUp" data-wow-delay="0.8s">
                    <p><strong>University of Central Punjab (UCP), Lahore</strong></p>
                    <p>Supervised by <strong>Prof. Muzammil Sadiq</strong></p>
                    <p>BSCS Final Year Project &mdash; Group <strong>G1F22FYPCS016</strong></p>
                </div>
            </div>
        </div>
    </section>
    <!-- team area end -->

    <!-- rts cta area start -->
    <section class="rts-cta-area rts-section-gap2 area-6">
        <div class="container-1340">
            <div class="row g-5 align-items-center">
                <div class="col-lg-5">
                    <div class="rts-cta-image-area bg-gray wow scaleIn" data-wow-delay="0.2s">
                        <img src="{{ asset('assets/images/cta/mobile.webp') }}" alt="mobile">
                        <div class="inner-shape">
                            <div class="inner"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="rts-cta-content-area">
                        <div class="section-title-area">
                            <h2 class="section-title rts-text-anime-style-1 text-transform-0 cw">Still Unsure About <br> the Source?</h2>
                            <p class="desc wow fadeInUp" data-wow-delay="0.4s">Paste the headline, article or screenshot into our verifier and we will tell you what the writing style suggests, whether the claim matches known records, and exactly how we reached that answer.</p>
                            <div class="cta-subscribe-area wow fadeInUp" data-wow-delay="0.4s">
                                <a href="#verifier" class="rts-btn btn-primary">
                                    Check a Story Now <svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M2.78609 0.636774C3.42696 1.41352 5.13873 1.95475 6.7051 2.30304C8.72196 2.75133 10.7973 2.8843 12.8309 2.61539C14.3541 2.41363 16.072 2.00892 17.0527 1.19981M14.9104 15.3317C14.2695 14.555 14.0633 12.7716 14.0189 11.1676C13.962 9.10235 14.2256 7.03943 14.8759 5.09401C15.3633 3.63687 16.087 2.02707 17.0676 1.21797M17.0602 1.2089L0.63638 14.7596" stroke="white" stroke-width="2" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- rts cta area end -->
@endsection
