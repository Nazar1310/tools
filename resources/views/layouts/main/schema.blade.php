@php
    $mainSchema = [
        "@context" => "https://schema.org",
        "@type" => "Organization",
        "name" => env('APP_NAME'),
        "url" => route('index'),
        "logo" => asset('img/logo.png'),
    ];
@endphp
<script type="application/ld+json">
    {!! json_encode($mainSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
