@extends('layouts.app')

@section('title', __('Registrerad tid'))

@section('content')

    <H1>@lang('Registrerad tid')</H1>

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

    @if(isset($workplaces) && count($workplaces) > 0)
        @foreach($workplaces as $workplace)
            @if(count($workplace->project_times) > 0)
                <label>{{$workplace->name}}</label>
                @foreach($workplace->project_times->sortBy('date') as $project_time)
                    @if($project_time->date > $mindate)

                        <a class="list-group-item list-group-item-action">
                            <div class="row">
                                <div class="col-lg-3 col-md-3 col-sm-3">
                                    {{$project_time->date}} {{$project_time->startstr()}}-{{$project_time->endstr()}}
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-2">
                                    @lang('Registrerat av') {{$project_time->registered_by_user->name}}
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-2">
                                    {{$project_time->users->count()}} @lang('deltagare')
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-1">
                                    <i class="fas fa-edit" onClick="window.location='/projecttime/{{$project_time->id}}/edit'"></i>
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-1" onClick="window.location='/projecttime/presence_list/{{$project_time->id}}'">
                                    <i class="fas fa-print"></i>
                                    <i class="fas fa-arrow-right"></i>
                                    <i class="fas fa-clipboard-list"></i>
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-1" onClick="window.location='/projecttime/attest_from_list/{{$project_time->id}}'">
                                    <i class="fas fa-clipboard-list"></i>
                                    <i class="fas fa-arrow-right"></i>
                                    <i class="fas fa-stamp"></i>
                                </div>
                            </div>
                        </a>

                    @endif

                @endforeach
                <br>
            @endif
        @endforeach
    @elseif(count(Auth::user()->project_times) > 0)
        <ul class="list-group mb-3 tracks">
            @foreach(Auth::user()->project_times->sortBy('date') as $project_time)
                @if($project_time->date > $mindate)
                    <li class="list-group-item d-flex justify-content-between lh-condensed nopadding {{$project_time->is_attested?"list-group-item-secondary":""}}">
                        @if($project_time->registered_by == Auth::user()->id && !$project_time->is_attested)
                            <a href="/projecttime/{{$project_time->id}}/edit">
                        @else
                            <a href="#">
                        @endif
                            <h6 class="my-0">{{$project_time->date}} {{$project_time->startstr()}}-{{$project_time->endstr()}} {{$project_time->registered_by != Auth::user()->id?'('._('Registrerat av').' '.$project_time->registered_by_user->name.')':''}}</h6>
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>
    @else
        @lang('Inga tidsregistreringar inlagda')
    @endif

@endsection
