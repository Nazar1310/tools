@extends('layouts.main.index')
@section('title', 'Not found')
@section('meta_description', '')
@section('content')
    <main class="site-main error-page container">
        <img src="{{asset('img/errors/404.svg')}}" alt="404">
        <div class="hero-links">
            <a href="{{ route('tools') }}" class="view-all-link">View All Tools &#10132;</a>
        </div>
    </main>
@stop
