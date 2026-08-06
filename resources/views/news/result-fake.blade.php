@extends(auth()->check() ? 'layouts.fni' : 'layouts.frontend')

@section('title', 'VeriFact AI | Result: FAKE')

@section('content')
@include('news.partials.result-content')
@endsection
