@extends('layouts.frontend')

@section('title', 'VeriFact AI - About Us & FYP Team')

@section('bodyClass', 'innerpage')

@section('content')
<section class="rts-feature-area area-6 rts-section-gap2 pt--120">
    <div class="container-1340">
        <div class="section-title-area center-style">
            <h1 class="section-title rts-text-anime-style-1 text-transform-0">About VeriFact AI</h1>
            <p class="desc wow fadeInUp" data-wow-delay="0.2s">
                VeriFact AI is a BS Computer Science Final Year Project developed at the University of Central Punjab, Gujranwala Campus.
                Our mission is to empower readers, journalists, and researchers with transparent AI-driven credibility diagnostics.
            </p>
        </div>

        <!-- Project Methodology -->
        <div class="row g-28 mt--40 justify-content-center">
            <div class="col-lg-4 col-md-6">
                <div class="feature-wrapper-6 text-center p-4">
                    <div class="icon mb-3">
                        <i class="fa-solid fa-brain text-primary" style="font-size: 36px;"></i>
                    </div>
                    <h4 class="title">Machine Learning</h4>
                    <p class="desc">Trained NLP models analyzing linguistic style, sentiment bias, capitalisation ratio, clickbait syntax, and sensational vocabulary patterns.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="feature-wrapper-6 text-center p-4">
                    <div class="icon mb-3">
                        <i class="fa-solid fa-scale-balanced text-primary" style="font-size: 36px;"></i>
                    </div>
                    <h4 class="title">Fact-Verification Layer</h4>
                    <p class="desc">Named Entity Recognition (NER) and claim triple extraction paired with knowledge base lookups, Wikidata integration, and Google Fact Check verification.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="feature-wrapper-6 text-center p-4">
                    <div class="icon mb-3">
                        <i class="fa-solid fa-wand-magic-sparkles text-primary" style="font-size: 36px;"></i>
                    </div>
                    <h4 class="title">AI Image Detection</h4>
                    <p class="desc">Multi-technique analysis including Error Level Analysis (ELA), EXIF metadata inspection, DCT frequency domain analysis, and pixel statistical checks.</p>
                </div>
            </div>
        </div>

        <!-- Team Section -->
        <div class="section-title-area center-style mt--80">
            <h2 class="section-title text-transform-0">Development Team</h2>
        </div>
        <div class="section-inner mt--40">
            <div class="row g-28 justify-content-center">
                @foreach ([
                    ['name' => 'Maaz Naveed', 'role' => 'Machine Learning & Backend Architecture', 'initials' => 'MN'],
                    ['name' => 'Jazil Mehmood', 'role' => 'Frontend & User Experience Design', 'initials' => 'JM'],
                    ['name' => 'Muhammad Abrar', 'role' => 'Data Engineering, Testing & Documentation', 'initials' => 'MA'],
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
            <div class="team-meta wow fadeInUp mt--40" data-wow-delay="0.8s">
                <p><strong>University of Central Punjab (UCP), Gujranwala Campus</strong></p>
                <p>Supervised by <strong>Prof. Muzammil Sadiq</strong></p>
                <p>BSCS Final Year Project &mdash; Group <strong>G1F22FYPCS016</strong></p>
            </div>
        </div>
    </div>
</section>
@endsection
