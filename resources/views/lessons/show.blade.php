@extends('layouts.app')

@section('title', $lesson->translateOrDefault(App::getLocale())->name)

@section('content')

<script src="https://player.vimeo.com/api/player.js"></script>

    <H1>{{$lesson->translateOrDefault(App::getLocale())->name}}</H1>

    <div class="card">
        <div class="card-body">

            @if(count($lesson->contents) > 0)
                @foreach($lesson->contents->sortBy('order')->skip($first_content_order) as $content)
                @if($content->type == 'pagebreak')
                    @break
                @endif

                @switch($content->type)
                    @case('vimeo')
                        <div style="max-width:250px">
                            <div class="vimeo-container">
                                <iframe id="vimeo_{{$content->id}}" src="https://player.vimeo.com/video/{{$content->content}}" width="0" height="0" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
                            </div>
                        </div>
                        <script type="text/javascript">
                            var iframePlayer = new Vimeo.Player(document.querySelector('#vimeo_{{$content->id}}'));
                            @if(Auth::user()->use_subtitles)
                                iframePlayer.enableTextTrack('{{substr(App::getLocale(), 0, 2)}}').catch(function(error) {/*Do nothing if subtitle is missing*/});
                            @else
                                iframePlayer.disableTextTrack().catch(function(error) {/*Do nothing if subtitle is missing*/});
                            @endif
                            iframePlayer.on('timeupdate', function(data){
                                window.focus();
                                TimeMe.resetIdleCountdown();
                            });
                        </script>
                        @break

                   @case('youtube')
                        <div style="max-width:250px">
                            <div class="vimeo-container">
                                <iframe id="youtube_{{$content->id}}" src="https://www.youtube.com/embed/{{$content->content}}" width="0" height="0" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
                            </div>
                        </div>
                        @break

                    @case('html')
                        {!!$content->translateOrDefault(App::getLocale())->text!!}
                        <br><br>
                        @break

                    @case('audio')
                        <audio controls controlsList="nodownload">
                            <source src="{{$content->url()}}" type="audio/mpeg">
                        </audio>
                        @break

                    @case('office')
                        <div class="embed-responsive embed-responsive-16by9">
                            <iframe class="embed-responsive-item" src="https://view.officeapps.live.com/op/embed.aspx?src={{env('APP_URL').$content->url()}}"></iframe>
                        </div>
                        <br>
                        @break

                    @case('image')
                        <img class="lessonimage" src="{{$content->url()}}">
                        <br>
                        @break

                    @case('file')
                        <a target="_blank" href="{{$content->url()}}">{{$content->filename()}}</a>
                        <br>
                        @break

                    @default
                        Unexpected content type!
                @endswitch

                @endforeach
            @endif

        </div>
    </div>

    <br>

    @if($pages > 1)
        @for ($p = 1; $p <= $pages; $p++)
            <a href="/lessons/{{$lesson->id}}/{{$p}}" class="btn btn-primary {{$page==$p?'disabled':''}}">@lang('Sida') {{$p}}</a>
        @endfor
        <br><br>
    @endif

    @if ($question)
        <a href="/test/{{$lesson->id}}" class="btn btn-primary">@lang('Fortsätt till testet')</a>
    @else
        <a href="/lessons/{{$lesson->id}}/finish" class="btn btn-primary">@lang('Färdig med denna lektion')</a>
    @endif

    @can('manage lessons')
        <br><br>
        <a href="/lessons/{{$lesson->id}}/edit" class="btn btn-primary">@lang('Redigera lektionen')</a>
        <a href="/lessons/{{$lesson->id}}/editquestions" class="btn btn-primary">@lang('Redigera frågor för lektion')</a>
        <a href="/lessons/{{$lesson->id}}/replicate" class="btn btn-primary">@lang('Kopiera lektionen')</a>
    @endcan

@endsection
