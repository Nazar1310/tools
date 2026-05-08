@if ($paginator->hasPages())
    <nav class="pagination" aria-label="Pagination navigation">
        {{-- Previous --}}
{{--        @if ($paginator->onFirstPage())--}}
{{--            <span>&laquo;</span>--}}
{{--        @else--}}
{{--            <a href="{{ $paginator->previousPageUrl() }}" class="pagination-link">&laquo;</a>--}}
{{--        @endif--}}

        {{-- Pages --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="dots">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <a href="#" class="pagination-link active">{{ $page }}</a>
                    @else
                        <a href="{{ $url }}" class="pagination-link">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
{{--        @if ($paginator->hasMorePages())--}}
{{--            <a href="{{ $paginator->nextPageUrl() }}" class="pagination-link">&raquo;</a>--}}
{{--        @else--}}
{{--            <span>&raquo;</span>--}}
{{--        @endif--}}
    </nav>
@endif
