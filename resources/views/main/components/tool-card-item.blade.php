@php
    use App\Models\Tool;
    /** @var $tool Tool */
@endphp
<a href="{{ $tool->getRoute() }}" class="list-tile" aria-label="{{ $tool->title }}">
    <div>
        <h3>{{ $tool->name }}</h3>
        <p>{{ $tool->desc }}</p>
    </div>
</a>
