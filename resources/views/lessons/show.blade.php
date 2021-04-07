@extends('layouts.app')

@section('title', $lesson->translateOrDefault(App::getLocale())->name)

@section('content')

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

<script src="https://player.vimeo.com/api/player.js"></script>

<script type="text/javascript">
    $(document).ready(function(){
        $('.flip-card').click(function(){
            $(this).toggleClass('flipped')
        });
    });
</script>

    <H1>{{$lesson->translateOrDefault(App::getLocale())->name}}</H1>

    <div class="card">
        <div class="card-body">

            @if(count($lesson->contents) > 0)
                @foreach($lesson->contents->sortBy('order')->skip($first_content_order) as $content)
                @if($content->type == 'pagebreak')
                    @break
                @endif
                <div class="clearfix">

                    @switch($content->type)
                        @case('vimeo')
                            <div class="{{$content->adjustment}}" style="width:100%;max-width:{{$content->max_width}}px">
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
                            <div class="{{$content->adjustment}}" style="width:100%;max-width:{{$content->max_width}}px">
                                <div class="vimeo-container">
                                    <iframe id="youtube_{{$content->id}}" src="https://www.youtube.com/embed/{{$content->content}}" width="0" height="0" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
                                </div>
                            </div>
                            @break

                        @case('html')
                            {!!$content->text!!}
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
                            <div class="{{$content->adjustment}}" style="max-width:{{$content->max_width}}px">
                                <img class="lessonimage" src="{{$content->url()}}">
                            </div>
                            <br>
                            @break

                        @case('flipcard')
                            <div class="card mb-3 {{$content->adjustment}}" style="border:0;width:{{$content->max_width}}px;height:{{$content->max_height}}px;max-width:100%">
                                <div class="flip-card mb-3">
                                    <div class="flip-card-inner" style="background-color:{{$content->color->hex}}">
                                        <div class="flip-card-front">
                                            <div class="flip-card-content">
                                                {!!$content->textPart(0)!!}
                                            </div>
                                        </div>
                                        <div class="flip-card-back">
                                            <div class="flip-card-content">
                                                {!!$content->textPart(1)!!}
                                            </div>
                                        </div>
                                    </div>  
                                </div>  
                            </div>

                            @break

                        @case('file')
                            <a target="_blank" href="{{$content->url()}}">{{$content->filename()}}</a>
                            <br>
                            @break

                        @case('toc')
                            @if($pages > 1)
                                @for ($p = 1; $p <= $pages; $p++)
                                    <a href="/lessons/{{$lesson->id}}/{{$p}}" style="{{$lesson->page_color_style($p)}}" class="btn btn-primary {{$page==$p?'disabled':''}}">{{$lesson->page_heading($p)}}</a>
                                    <br><br>
                                @endfor
                            @else
                                Please add more pages in order to make a table of contents!
                            @endif
                            <br>
                            @break

                        @default
                            Unexpected content type!
                    @endswitch
                </div>
                @endforeach
            @endif

        </div>
    </div>

    <br>

    @if($pages > 1)
        <a href="/lessons/{{$lesson->id}}/{{$page-1}}" class="btn btn-primary {{$page==1?'disabled':''}}"><i class="fas fa-chevron-left"></i></a>
        @for ($p = 1; $p <= $pages; $p++)
            <a href="/lessons/{{$lesson->id}}/{{$p}}" style="{{$lesson->page_color_style($p)}}" class="btn btn-primary {{$page==$p?'disabled':''}}">{{$lesson->page_heading($p)}}</a>
        @endfor
        <a href="/lessons/{{$lesson->id}}/{{$page+1}}" class="btn btn-primary {{$page==$pages?'disabled':''}}"><i class="fas fa-chevron-right"></i></a>
        <br><br>
    @endif

    @if ($question)
        <a href="/test/{{$lesson->id}}" class="btn btn-primary">@lang('Fortsätt till testet')</a>
    @elseif($lesson->poll)
        <a href="/poll/{{$lesson->poll->id}}" class="btn btn-primary">@lang('Utvärdera denna modul')</a>        
    @else
        <a href="/lessons/{{$lesson->id}}/finish" class="btn btn-primary">@lang('Färdig med denna modul')</a>
    @endif

    <a href="/tracks/{{$lesson->track->id}}" class="btn btn-primary">@lang('Tillbaka till spåret')</a>

    @if($is_editor)
        <br><br>
        <a href="/lessons/{{$lesson->id}}/edit" class="btn btn-primary">@lang('Redigera modulen')</a>
        <a href="/lessons/{{$lesson->id}}/editquestions" class="btn btn-primary">@lang('Redigera modulens test')</a>
        <a href="/lessons/{{$lesson->id}}/replicate" class="btn btn-primary">@lang('Kopiera modulen')</a>
    @endif

@endsection
