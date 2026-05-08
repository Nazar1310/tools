@php
    $breadcrumb = [
        [ 'name' => 'Home', 'url' => route('index'), 'isLast' => false ],
        [ 'name' => 'All Categories', 'url' => route('categories'), 'isLast' => true ],
    ];
@endphp

@extends('layouts.main.index')
@section('title', 'All Categories')
@section('meta_description', '')

@section('content')
    <main class="site-main container">
        @include('main.components.breadcrumb', ['items' => $breadcrumb])

        <section class="hero">
            <h1>All Categories</h1>
            <p>Select a category to discover tools for your needs.</p>
        </section>

        <section class="list-grid">
            @foreach($categories as $category)
                @include('main.components.category-card-item')
            @endforeach
        </section>

        {{ $categories->links('main.components.pagination') }}

        <section class="mt-3 text-center">
            <p class="text-block">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
        </section>
    </main>
@stop
