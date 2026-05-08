@php
    /** @var $faqItems array */
    $indexSchema = [
        "@context" => "https://schema.org",
        "@type" => "FAQPage",
        "mainEntity" => collect($faqItems)->map(function ($faq) {
            return [
                "@type" => "Question",
                "name" => $faq['question'],
                "acceptedAnswer" => [
                    "@type" => "Answer",
                    "text" => $faq['answer'],
                ],
            ];
        })->values()->toArray()
    ];
@endphp
@extends('layouts.main.index')

@section('title', 'Tools')
@section('meta_description', '')

@section('schema')
    <script type="application/ld+json">
        {!! json_encode($indexSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>
@stop

@section('content')
    <main class="site-main homepage container">
        <section class="hero">
            <h1>Tools</h1>
            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
            <form class="d-flex jcc" action="{{ route('search') }}" method="GET">
                <input type="search" name="query" aria-label="Search tools" placeholder="Search tools..." required>
                <button type="submit" aria-label="Search">&#9740;</button>
            </form>
        </section>

        <section>
            <h2 class="sub-title">Popular tools</h2>
            <p class="sub-title-desc">Our most used tools to help with everyday planning and decisions.</p>
            <div class="list-grid">
                @foreach($tools as $tool)
                    @include('main.components.tool-card-item')
                @endforeach
            </div>
        </section>

        <div class="hero-links mb-3">
            <a href="{{ route('tools') }}" class="view-all-link">View All Tools &#10132;</a>
        </div>

        <section>
            <h2 class="sub-title">Categories</h2>
            <p class="sub-title-desc">Explore tools by topic for quick, tailored results.</p>
            <div class="list-grid">
                @foreach($categories as $category)
                    @include('main.components.category-card-item')
                @endforeach
            </div>
        </section>

        <div class="hero-links">
            <a href="{{ route('categories') }}" class="view-all-link">View All Categories &#10132;</a>
        </div>

        <section class="faq-section mb-0">
            <h2 class="sub-title">Frequently Asked Questions</h2>
            @foreach($faqItems as $faq)
                <div class="{{ $loop->last ? 'mb-0' : '' }}">
                    <h3>{{$faq['question']}}</h3>
                    <p>{{$faq['answer']}}</p>
                </div>
            @endforeach
        </section>
    </main>
@stop
