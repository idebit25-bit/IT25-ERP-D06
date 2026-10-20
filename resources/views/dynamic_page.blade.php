@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ $page->title }}</h1>
    <p>{!! nl2br(e($page->content)) !!}</p>

    <div class="navigation">
        <a href="{{ url()->previous() }}" class="btn btn-dark">VOLVER</a>
        @php
            $nextPage = App\Models\Page::where('order', '>', $page->order)->orderBy('order', 'asc')->first();
        @endphp
        @if($nextPage)
            <a href="{{ route('pages.show', $nextPage->slug) }}" class="btn btn-dark">SIGUIENTE</a>
        @endif
    </div>
</div>
@endsection
