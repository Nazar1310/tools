@php
    $breadcrumb = [
        [ 'name' => 'Home', 'url' => route('index'), 'isLast' => false ],
        [ 'name' => 'Terms of Use', 'url' => route('terms'), 'isLast' => true ],
    ];
@endphp

@extends('layouts.main.index')

@section('title', 'Terms of Use')
@section('meta_description', '')

@section('content')
    <main class="site-main container">
        @include('main.components.breadcrumb', ['items' => $breadcrumb])

        <section class="info">
            <h1>Terms of Use</h1>
            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
        </section>
    </main>
@stop
