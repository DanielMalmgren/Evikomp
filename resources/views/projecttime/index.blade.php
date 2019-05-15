@extends('layouts.app')

@section('title', __('Registrerad tid'))

@section('content')

    <H1>@lang('Registrerad tid')</H1>

    @if(isset($workplaces) && count($workplaces) > 0)
        <ul class="list-group mb-3 tracks">
            @foreach($workplaces as $workplace)
                <label>{{$workplace->name}}</label>
                @if(count($workplace->project_times) > 0)
                    @foreach($workplace->project_times->sortBy('date') as $project_time)
                        <li class="list-group-item d-flex justify-content-between lh-condensed nopadding">
                            <a href="/projecttime/{{$project_time->id}}/edit">
                                <h6 class="my-0">{{$project_time->date}} {{$project_time->startstr()}}-{{$project_time->endstr()}} (@lang('Registrerat av') {{$project_time->registered_by_user->name}})</h6>
                            </a>
                        </li>
                    @endforeach
                @else
                    @lang('Inga tidsregistreringar på denna arbetsplats')
                @endif
                <br>
            @endforeach
        </ul>
    @elseif(count(Auth::user()->project_times) > 0)
        <ul class="list-group mb-3 tracks">
            @foreach(Auth::user()->project_times->sortBy('date') as $project_time)
                <li class="list-group-item d-flex justify-content-between lh-condensed nopadding">
                    @if($project_time->registered_by == Auth::user()->id)
                        <a href="/projecttime/{{$project_time->id}}/edit">
                    @else
                        <a href="#">
                    @endif
                        <h6 class="my-0">{{$project_time->date}} {{$project_time->startstr()}}-{{$project_time->endstr()}} {{$project_time->registered_by != Auth::user()->id?'('._('Registrerat av').' '.$project_time->registered_by_user->name.')':''}}</h6>
                    </a>
                </li>
            @endforeach
        </ul>
    @else
        @lang('Inga tidsregistreringar inlagda')
    @endif

@endsection
