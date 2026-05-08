@php
    /** @var $items array */

    $breadcrumbSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => collect($items)->map(function ($item, $key) {
            return [
                '@type' => 'ListItem',
                'position' => $key + 1,
                'name' => $item['name'],
                'item' => $item['url'],
            ];
        })->values()->toArray()
    ];
@endphp
<script type="application/ld+json">
    {!! json_encode($breadcrumbSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
<nav aria-label="breadcrumb" class="breadcrumb">
    <ol>
        @foreach($items as $item)
            <li>
                @if(!$item['isLast'])
                    <a href="{{ $item['url'] }}">
                        <span>{{ $item['name'] }}</span>
                    </a>
                @else
                    <span aria-current="page">{{ $item['name'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
