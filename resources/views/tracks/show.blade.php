@extends('layouts.app')

@section('title', $track->translateOrDefault(App::getLocale())->name)

@section('content')

    <H1>
        <img class="lessonimage" src="{{$track->icon_with_path()}}" style="max-width:40px;max-height:40px">
        {{$track->translateOrDefault(App::getLocale())->name}}
    </H1>

    @if(count($lessons) > 0)
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
        <div class="list-group mb-4 lessonslist" id="lessonslist">
            @foreach($lessons as $lesson)
                @include('inc.listlesson')
            @endforeach
        </div>
    @endif

    @if($is_editor)
        @if(locale_is_default())
            <a href="/lessons/create/{{$track->id}}" class="btn btn-secondary">@lang('Lägg till modul')</a>
            @can('Import SCORM')
                <a href="/scormimport/create/{{$track->id}}" class="btn btn-secondary">@lang('Importera SCORM-fil')</a>
            @endcan
        @endif

        <script type="text/javascript" language="javascript" src="{{asset('vendor/jquery-ui-1.12.1.custom/jquery-ui.min.js')}}"></script>
        <script type="text/javascript">
            $(function() {
                $("#lessonslist").sortable({
                update: function (e, u) {
                    var token = "{{ csrf_token() }}";
                    var data = $(this).sortable('serialize');
                    $.ajax({
                        url: '/lessons/reorder',
                        data : {_token:token,data:data},
                        type: 'POST'
                    });
                }
                });
            });
        </script>
    @endif
    
    @can('Manage lessons')
        <a href="/tracks/{{$track->id}}/edit" class="btn btn-secondary">@lang('Redigera spåret')</a>
    @endcan

    @can('export track compilation')
        <a href="/tracks/{{$track->id}}/compilationxls" class="btn btn-secondary">@lang('Exportera resultat')</a>
    @endcan
    @hasrole('Admin')
        <a href="/log?subject_id={{$track->id}}&subject_type=App\Track" class="btn btn-secondary">@lang('Visa logg')</a>
    @endhasrole

@endsection
