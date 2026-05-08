@php
    use App\Models\Category;
    /** @var $category Category */

    $breadcrumb = [
        [ 'name' => 'Home', 'url' => route('index'), 'isLast' => false ],
        [ 'name' => $category->name, 'url' => $category->getRoute(), 'isLast' => true ],
    ];
@endphp

@extends('layouts.main.index')

@section('title', $category->seo_title)
@section('meta_description', $category->seo_desc)

@section('content')
    <main class="site-main container">
        @include('main.components.breadcrumb', ['items' => $breadcrumb])

        <section class="hero">
            <h1>{{ $category->title }}</h1>
            <p>{{ $category->desc }}</p>
        </section>

        <section class="list-grid">
            @foreach($tools as $tool)
                @include('main.components.tool-card-item')
            @endforeach
        </section>

        {{ $tools->links('main.components.pagination') }}
    </main>
@stop
