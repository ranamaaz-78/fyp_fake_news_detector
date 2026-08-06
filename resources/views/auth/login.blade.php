@extends('layouts.frontend')

@section('title', 'Sign In - VeriFact AI')

@section('bodyClass', 'innerpage')

@section('content')
    <!-- rts sign in area start -->
    <section class="rts-sign-in-area" data-bg-src="{{ asset('assets/images/banner/10.webp') }}" style="padding: 120px 0;">
        <div class="container-1320">
            <div class="section-title-area center-style">
                <div class="logo" style="text-align: center; margin-bottom: 20px;">
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('assets/images/Color logo.svg') }}" alt="logo" style="max-height: 50px;">
                    </a>
                </div>
                <h2 class="h3 section-title">Sign In</h2>
                <p class="desc">Access the VeriFact AI dashboard to monitor audits and manage APIs.</p>
            </div>
            <div class="section-inner mt--60">
                <div class="row justify-content-center">
                    <div class="col-lg-6">
                        <div class="rts-sign-in-wrapper">
                            @if ($errors->any())
                                <div class="alert alert-danger mb-4" role="alert">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if (session('status'))
                                <div class="alert alert-success mb-4" role="alert">
                                    {{ session('status') }}
                                </div>
                            @endif

                            <form method="POST" action="{{ route('login') }}" class="rts-sign-in-form">
                                @csrf

                                <!-- Email -->
                                <div class="single-input">
                                    <label for="email">Email address</label>
                                    <span class="icon">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M17.9024 8.85156L13.4591 12.4646C12.6196 13.1306 11.4384 13.1306 10.5989 12.4646L6.11816 8.85156" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M16.9089 21C19.9502 21.0084 22 18.5095 22 15.4384V8.57001C22 5.49883 19.9502 3 16.9089 3H7.09114C4.04979 3 2 5.49883 2 8.57001V15.4384C2 18.5095 4.04979 21.0084 7.09114 21H16.9089Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                    <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="Enter your email here" required autofocus autocomplete="username">
                                </div>

                                <!-- Password -->
                                <div class="single-input">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label for="password" class="mb-0">Password</label>
                                        @if (Route::has('password.request'))
                                            <a href="{{ route('password.request') }}" class="forgot-btn" style="font-size: 14px; color: var(--color-primary); font-weight: 500;">Forgot password?</a>
                                        @endif
                                    </div>
                                    <span class="icon" style="top: 48px;">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M16.4228 9.44804V7.30104C16.4228 4.78804 14.3848 2.75004 11.8718 2.75004C9.35876 2.73904 7.31276 4.76704 7.30176 7.28104V7.30104V9.44804" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M15.683 21.248H8.042C5.948 21.248 4.25 19.551 4.25 17.456V13.167C4.25 11.072 5.948 9.375 8.042 9.375H15.683C17.777 9.375 19.475 11.072 19.475 13.167V17.456C19.475 19.551 17.777 21.248 15.683 21.248Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M11.8623 14.2031V16.4241" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                    <input type="password" name="password" id="password" placeholder="Enter your password here" required autocomplete="current-password" style="margin-top: 8px;">
                                </div>

                                <!-- Remember Me -->
                                <div class="single-input remember-me d-flex align-items-center gap-2 mb-4">
                                    <input type="checkbox" name="remember" id="remember" style="width: auto; margin-bottom: 0;">
                                    <label for="remember" class="mb-0" style="cursor: pointer;">Remember me</label>
                                </div>

                                <!-- Login Button -->
                                <div class="single-input">
                                    <button type="submit" class="rts-btn btn-primary w-100 justify-content-center">
                                        Login
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path d="M20.75 11.7266L3.75 11.7266" stroke="white" stroke-width="1.8" stroke-linecap="round" />
                                            <path d="M20.7531 11.725L14.7031 17.75" stroke="white" stroke-width="1.8" stroke-linecap="round" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Sign Up -->
                                <p class="signup-text mt-4 text-center">Don't have an account? <a href="{{ route('register') }}" style="color: var(--color-primary); font-weight: 600;">Sign up</a></p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- rts sign in area end -->
@endsection
