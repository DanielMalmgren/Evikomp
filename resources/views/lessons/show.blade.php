@extends('layouts.app')

@section('title', $lesson->translateOrDefault(App::getLocale())->name)

@section('content')

    <H1>{{$lesson->translateOrDefault(App::getLocale())->name}}</H1>

    {{$lesson->translateOrDefault(App::getLocale())->description}}

    <div class="vimeo-container">
        <iframe src="https://player.vimeo.com/video/{{$lesson->video_id}}" class="vimeo-iframe" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
    </div>

    <br>

    @if ($question)
        <a href="/test/{{$lesson->id}}" class="btn btn-primary">@lang('Fortsätt till testet')</a>
    @else
        <a href="/test/{{$lesson->id}}" class="btn btn-primary disabled">@lang('Fortsätt till testet')</a>
    @endif

    @can ('manage lessons')
        <a href="/lessons/{{$lesson->id}}/edit" class="btn btn-primary">@lang('Redigera denna lektion')</a>
    @endcan

@endsection
