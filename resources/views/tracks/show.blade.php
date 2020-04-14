@extends('layouts.app')

@section('title', $track->translateOrDefault(App::getLocale())->name)

@section('content')

    <H1>{{$track->translateOrDefault(App::getLocale())->name}}</H1>

    @if(count($lessons) > 0)
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
        <div class="list-group mb-4 lessonslist" id="lessonslist">
            @foreach($lessons as $lesson)
                @include('inc.listlesson')
            @endforeach
        </div>
    @endif

    @can('manage lessons')
        <a href="/lessons/create/{{$track->id}}" class="btn btn-primary">@lang('Lägg till lektion')</a>

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
    @endcan

    @can ('manage lessons')
        <a href="/tracks/{{$track->id}}/edit" class="btn btn-primary">@lang('Redigera spåret')</a>
    @endcan

@endsection
