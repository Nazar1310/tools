<header class="site-header">
    <div class="container">
        <div class="logo">
            <img
                src="{{ asset('img/logo.png') }}"
                alt="{{ env('APP_NAME') }}"
                title=""
            />
        </div>
        <form class="d-flex jcc" action="{{ route('search') }}" method="GET">
            <input
                type="search"
                name="query"
                aria-label="Search tools"
                placeholder="Search tools..."
                class="search-input"
                value="{{ request('query') }}"
                required
            >
            <button type="submit" class="search-button" aria-label="Search">&#9740;</button>
        </form>
    </div>
</header>
