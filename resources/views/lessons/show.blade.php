@extends('layouts.app')

@section('title', $lesson->translateOrDefault(App::getLocale())->name)

@section('content')

    <H1>{{$lesson->translateOrDefault(App::getLocale())->name}}</H1>

    {{--{!!$lesson->translateOrDefault(App::getLocale())->description!!}

    <div class="vimeo-container">
        <iframe src="https://player.vimeo.com/video/{{$lesson->video_id}}" class="vimeo-iframe" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
    </div>--}}

    @if(count($lesson->contents) > 0)
        @foreach($lesson->contents->sortBy('order') as $content)
        @switch($content->type)
            @case('vimeo')
                <div class="vimeo-container">
                    <iframe src="https://player.vimeo.com/video/{{$content->content}}" class="vimeo-iframe" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
                </div>
                @break

            @case('html')
                {!!$content->translateOrDefault(App::getLocale())->text!!}
                @break

            @case('audio')
                <audio controls controlsList="nodownload">
                    <source src="/storage/pods/{{$content->content}}" type="audio/mpeg">
                </audio>
                @break

            @default
                Unexpected content type!
        @endswitch
        <br>
        @endforeach
    @endif

    <br>

    @if ($question)
        <a href="/test/{{$lesson->id}}" class="btn btn-primary">@lang('Fortsätt till testet')</a>
    @else
        <a href="/test/{{$lesson->id}}" class="btn btn-primary disabled">@lang('Fortsätt till testet')</a>
    @endif

    @can ('manage lessons')
        <a href="/lessons/{{$lesson->id}}/edit" class="btn btn-primary">@lang('Redigera lektionen')</a>
        <a href="/lessons/{{$lesson->id}}/editquestions" class="btn btn-primary">@lang('Redigera lektionens frågor')</a>
    @endcan

@endsection
