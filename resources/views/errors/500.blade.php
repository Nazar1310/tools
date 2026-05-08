@extends('layouts.main.index')
@section('title', 'Server error')
@section('meta_description', '')
@section('content')
    <main class="site-main error-page container">
        <img class="not-found" src="{{asset('img/errors/500.svg')}}" alt="500">
        <div class="hero-links">
            <a href="{{ route('index') }}" class="view-all-link">Go Homepage &#10132;</a>
        </div>
    </main>
@stop
