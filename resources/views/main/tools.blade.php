@php
    $breadcrumb = [
        [ 'name' => 'Home', 'url' => route('index'), 'isLast' => false ],
        [ 'name' => 'All Tools', 'url' => route('tools'), 'isLast' => true ],
    ];
@endphp

@extends('layouts.main.index')

@section('title', 'All Tools')
@section('meta_description', '')

@section('content')
    <main class="site-main container">
        @include('main.components.breadcrumb', ['items' => $breadcrumb])

        <section class="hero">
            <h1>All Tools</h1>
            <p>Browse through all available tools by category, topic or keyword.</p>
        </section>

        <section class="list-grid">
            @foreach($tools as $tool)
                @include('main.components.tool-card-item')
            @endforeach
        </section>

        {{ $tools->links('main.components.pagination') }}

        <section class="mt-3 text-center">
            <p class="text-block">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
        </section>
    </main>
@stop
