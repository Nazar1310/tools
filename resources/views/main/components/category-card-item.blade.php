@php
    use App\Models\Category;
    /** @var $category Category */
@endphp
<a href="{{ $category->getRoute() }}" class="list-tile mb-0" aria-label="{{ $category->title }}">
    <div>
        <h3>{{ $category->name }}</h3>
    </div>
</a>
