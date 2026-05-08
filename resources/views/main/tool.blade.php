@php
    use App\Models\Category;
    use App\Models\Tool;
    /** @var $tool Tool */
    /** @var $category Category */

    $breadcrumb = [
        [ 'name' => 'Home', 'url' => route('index'), 'isLast' => false ],
        [ 'name' => $category->name, 'url' => $category->getRoute(), 'isLast' => false ],
        [ 'name' => $tool->name, 'url' => $tool->getRoute(), 'isLast' => true ],
    ];

    $toolSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'WebApplication',
        'name' => $tool->name,
        'url' => $tool->getRoute(),
        'applicationCategory' => $category->name,
        'operatingSystem' => 'All',
        'description' => $tool->seo_desc,
        'inLanguage' => 'en',
        'offers' => [
            '@type' => 'Offer',
            'price' => '0',
            'priceCurrency' => 'USD',
        ],
        'publisher' => [
            '@type' => 'Organization',
            'name' => env('APP_NAME'),
            'url' => route('index'),
        ],
    ];
@endphp

@extends('layouts.main.index')
@section('title', $tool->seo_title)
@section('meta_description', $tool->seo_desc)

@section('schema')
    <script type="application/ld+json">
        {!! json_encode($toolSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>
@stop

@section('content')
    <main class="site-main container w-100">
        @include('main.components.breadcrumb', ['items' => $breadcrumb])

        <section>
            <h1 class="tool-title">{{ $tool->title }}</h1>
            <p class="tool-intro">{{ $tool->desc }}</p>
        </section>

        @include("tools.$category->slug.$tool->slug.core")
        @include("tools.$category->slug.$tool->slug.seo")

        @if(sizeof($similarTools))
            <section>
                <h2 class="sub-title">Similar Tools</h2>
                <div class="list-grid">
                    @foreach($similarTools as $item)
                        @include('main.components.tool-card-item', ['tool' => $item])
                    @endforeach
                </div>
            </section>
        @endif
        @if(sizeof($similarTools) >= 4)
            <div class="hero-links">
                <a href="{{ $category->getRoute() }}" class="view-all-link">View More Tools &#10132;</a>
            </div>
        @endif
    </main>
@stop
