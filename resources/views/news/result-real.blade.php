@extends(auth()->check() ? 'layouts.fni' : 'layouts.frontend')

@section('title', 'VeriFact AI | Result: REAL')

@section('content')
@include('news.partials.result-content')
@endsection
