@extends('layouts.app')

@section('title', __('Registrerade lärtillfällen'))

@section('content')

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

    <link rel="stylesheet" type="text/css" media="all" href="{{asset('fullcalendar/main.min.css')}}">
    <script type="text/javascript" language="javascript" src="{{asset('fullcalendar/main.min.js')}}"></script>
    <script type="text/javascript" language="javascript" src="{{asset('fullcalendar/locales-all.min.js')}}"></script>

    <H1>@lang('Registrerade lärtillfällen')</H1>

    <ul class="nav nav-tabs tabs-up" id="charts">
        <li class="nav-item"><a href="#" data-target="#calendar" class="nav-link active" data-toggle="tabchange" rel="tooltip"> @lang('Kalender') </a></li>
        <li class="nav-item"><a href="#" data-target="#list" class="nav-link" data-toggle="tabchange" rel="tooltip"> @lang('Lista') </a></li>
    </ul>

    <div class="tab-content">

        <div id="calendar" class="tab-pane show active">
            <br>
            {!! $calendar->calendar() !!}
            {!! $calendar->script() !!}
        </div>

        <div id="list" class="tab-pane">
            <br>
            @foreach($project_times as $project_time)
                <a class="list-group-item list-group-item-action">
                    <div class="row">
                        <div class="col-lg-3 col-md-3 col-sm-3">
                            {{$project_time->date}} {{$project_time->startstr()}}-{{$project_time->endstr()}}
                        </div>
                        <div class="col-lg-2 col-md-3 col-sm-2">
                            {{$project_time->workplace->name}}
                        </div>
                        <div class="col-lg-2 col-md-3 col-sm-2">
                            {{$project_time->registered_by_user->name}}
                        </div>
                        <div class="col-lg-1 col-md-2 col-sm-2">
                            {{$project_time->users->count()}}
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1">
                            <i class="fas fa-edit" onClick="window.location='/projecttime/{{$project_time->id}}/edit'"></i>
                        </div>
                        @if($project_time->users->count() > 1)
                            <div class="col-lg-1 col-md-1 col-sm-1" onClick="window.location='/projecttime/presence_list/{{$project_time->id}}'">
                                <i class="fas fa-print"></i>
                                <i class="fas fa-arrow-right"></i>
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                        @else
                            <div class="col-lg-1 col-md-1 col-sm-1"></div>
                        @endif
                        @hasrole('Admin')
                            @if($project_time->time_attests->isEmpty() && $project_time->users->count() > 1)
                                <div class="col-lg-1 col-md-1 col-sm-1" onClick="window.location='/projecttime/attest_from_list/{{$project_time->id}}'">
                                    <i class="fas fa-clipboard-list"></i>
                                    <i class="fas fa-arrow-right"></i>
                                    <i class="fas fa-stamp"></i>
                                </div>
                            @endif
                        @endhasrole
                    </div>
                </a>
            @endforeach
        </div>
    </div>

<script type="text/javascript">
    $('[data-toggle="tabchange"]').click(function(e) {
        $(this).tab('show');
        return false;
    });

    function onDateClick(dateClickInfo) {
        window.location='/projecttime/create?date='+dateClickInfo.dateStr+'&allDay='+dateClickInfo.allDay;
    }
</script>

@endsection
