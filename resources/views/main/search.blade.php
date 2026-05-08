@php
    $breadcrumb = [
        [ 'name' => 'Home', 'url' => route('index'), 'isLast' => false ],
        [ 'name' => 'Search', 'url' => route('search'), 'isLast' => true ],
    ];
@endphp

@extends('layouts.main.index')

@section('title', 'Search Results')
@section('meta_description', '')

@section('content')
    <main class="site-main container w-100">
        @include('main.components.breadcrumb', ['items' => $breadcrumb])

        <section class="hero">
            <h1 class="mb-0">Search Results for "{{ $query }}"</h1>
        </section>

        <section class="list-grid">
            @forelse($tools as $tool)
                @include('main.components.tool-card-item')
            @empty
                <p class="text-center">No tools found. Try a different search term.</p>
            @endforelse
        </section>
    </main>
@stop
