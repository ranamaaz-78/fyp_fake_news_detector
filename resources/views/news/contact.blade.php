@extends('layouts.frontend')

@section('title', 'Contact - VeriFact AI')

@section('bodyClass', 'innerpage')

@section('content')
<!-- rts contact area start -->
    <section class="rts-contact-area" data-bg-src="{{ asset(\'assets/images/banner/10.webp\') }}">
        <div class="container-1320">
            <div class="section-title-area center-style">
                <p class="sub-title text-uppercase"><span class="dot"></span>Contact Us</p>
                <h2 class="h3 section-title wow fadeInUp" data-wow-delay=".2s">Get In Touch With <br> <span>VeriFact AI</span></h2>
                <p class="desc wow fadeInUp" data-wow-delay=".4s">Have questions, need API credentials, or want custom integration solutions? Reach out to our credibility experts.</p>
            </div>
            <div class="section-inner mt--60">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="rts-contact-form-area">
                            <form class="contact-form" id="contact-form" action="#" method="post" enctype="multipart/form-data">
                                <div class="single-input-wrapper">
                                    <label>Name</label>
                                    <input type="text" name="name" id="name" placeholder="Enter your name" required>
                                </div>
                                <div class="single-input-wrapper">
                                    <label>Work Email</label>
                                    <input type="email" name="email" id="email" placeholder="Enter your email address" required>
                                </div>
                                <div class="single-input-wrapper">
                                    <label>Company Name</label>
                                    <input type="text" name="company" id="company" placeholder="Enter company name" required>
                                </div>
                                <div class="single-input-wrapper">
                                    <label>Job Title</label>
                                    <input type="text" name="job_title" id="job_title" placeholder="Enter job title" required>
                                </div>
                                <div class="single-input-row">
                                    <div class="single-input-wrapper">
                                        <label>Company size</label>
                                        <input type="text" name="company_size" id="company_size" placeholder="Enter company size" required>
                                    </div>
                                    <div class="single-input-wrapper">
                                        <label>Industry</label>
                                        <select name="industry" id="industry">
                                            <option selected disabled>Select</option>
                                            <option>Journalism / News</option>
                                            <option>Fact Checking</option>
                                            <option>Social Media Platforms</option>
                                            <option>Education / Academic</option>
                                            <option>Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="single-input-wrapper">
                                    <label>What are you hoping to use VeriFact AI for?</label>
                                    <textarea name="message" id="message" placeholder="Enter your comments or requirements" required></textarea>
                                </div>
                                <div class="single-input-wrapper">
                                    <label>Upload Documents (optional)</label>
                                    <div class="upload-box">
                                        <input type="file" name="attachment" id="attachment">
                                        <span class="icon">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M7.38948 8.98256H6.45648C4.42148 8.98256 2.77148 10.6326 2.77148 12.6676V17.5426C2.77148 19.5766 4.42148 21.2266 6.45648 21.2266H17.5865C19.6215 21.2266 21.2715 19.5766 21.2715 17.5426V12.6576C21.2715 10.6286 19.6265 8.98256 17.5975 8.98256L16.6545 8.98256" stroke="#05011C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M12.0215 2.18947V14.2305" stroke="#05011C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M9.10645 5.11719L12.0214 2.18919L14.9374 5.11719" stroke="#05011C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </span>
                                        <p>Click to upload your documentation, rumor list, or integration guidelines.</p>
                                        <span>max : 20MB</span>
                                    </div>
                                </div>
                                <button type="submit" class="rts-btn btn-primary">
                                    Send Message
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M20.75 11.7266L3.75 11.7266" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M20.7531 11.725L14.7031 17.75" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>
                            </form>

                            <div id="form-messages" class="form-messages"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- rts contact area end -->

    <!-- rts faq area start -->
    <section class="rts-faq-area area-2 rts-section-gap2" data-bg-src="{{ asset(\'assets/images/faq/faq-bg.webp\') }}">
        <div class="container">
            <div class="section-inner">
                <div class="section-title-area">
                    <div class="left">
                        <p class="sub-title"><img src="{{ asset(\'assets/images/feature/icon/11.svg\') }}" alt="">FAQ</p>
                        <h2 class="h3 section-title">Frequently Asked Questions <br> <span>About VeriFact AI</span>
                        </h2>
                    </div>
                </div>

                <div class="rts-accordion accordion-flush" id="rts-accordion">
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
    <section class="rts-cta-area area-2 rts-section-gapTop">
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
