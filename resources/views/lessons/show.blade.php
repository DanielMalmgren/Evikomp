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
                                    <iframe id="vimeo_{{$content->id}}" src="https://player.vimeo.com/video/{{$content->content}}{{$content->hash_for_embedding}}" width="0" height="0" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
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

                        @case('google')
                            <div style="border-width:1px;border: solid #000;" class="embed-responsive embed-responsive-16by9">
                                {!!$content->content!!}
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
                                    <div class="flip-card-inner">
                                        <div class="flip-card-front" style="background-color:{{$content->color->hex}}">
                                            <div class="flip-card-content">
                                                {!!$content->textPart(0)!!}
                                            </div>
                                        </div>
                                        <div class="flip-card-back" style="background-color:{{$content->color->hex}}">
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

    <a href="/result/{{$lesson->id}}" class="btn btn-primary">@lang('Färdig med denna modul')</a>

    @can('see beta features')
        <div class="modal fade" id="module-management" tabindex="-1" role="dialog" aria-labelledby="module-management-label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="module-management-label">Hantera listor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Stäng">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @if($my_lists->isEmpty())
                    @lang('Du har inte skapat några listor än, klicka på "Skapa ny lista" för att skapa din första lista!')
                @else
                    <p>@lang('Välj i vilka listor denna modul ska finnas')</p>
                    @foreach($my_lists as $list)
                        <label><input class="lessonconnect" data-list="{{$list->id}}" type="checkbox" {{$lesson->lesson_lists->contains('id', $list->id)?"checked":""}}>{{$list->name}}</label><br>
                    @endforeach
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary mr-auto" data-dismiss="modal">@lang('Stäng')</button>
                <a href="/lists/create?lesson_id={{$lesson->id}}" class="btn btn-secondary">@lang('Skapa ny lista')</a>
                <a href="/lists" class="btn btn-secondary">@lang('Redigera dina listor')</a>
            </div>
            </div>
        </div>
        </div>

        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#module-management">
            @lang('Hantera listor')
        </button>

        <script type="text/javascript">
                $(function() {
                    $('.lessonconnect').change(function() {
                        var token = "{{ csrf_token() }}";
                        $.ajax({
                            url: '/lists/lessonattach',
                            data : {
                                _token:token,
                                attach:this.checked,
                                list:this.dataset.list,
                                lesson:{{$lesson->id}}
                            },
                            type: 'POST'
                        });
                    });                
                });
        </script>
    @endcan

    <a href="/tracks/{{$lesson->track->id}}" class="btn btn-secondary">@lang('Tillbaka till spåret')</a>

    @if($is_editor)
        <br><br>
        <a href="/lessons/{{$lesson->id}}/edit" class="btn btn-primary">@lang('Redigera modulen')</a>
        <a href="/lessons/{{$lesson->id}}/editquestions" class="btn btn-primary">@lang('Redigera modulens test')</a>
        <a href="/notificationreceivers/{{$lesson->id}}/edit" class="btn btn-primary">@lang('Redigera notifieringsmottagare')</a>
        <a href="/lessons/{{$lesson->id}}/replicate" class="btn btn-primary">@lang('Kopiera modulen')</a>
    @endif
    @hasrole('Admin')
        <a href="/log?subject_id={{$lesson->id}}&subject_type=App\Lesson" class="btn btn-secondary">@lang('Visa logg')</a>
    @endhasrole


@endsection
