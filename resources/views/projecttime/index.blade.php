@extends('layouts.app')

@section('title', __('Registrerad tid'))

@section('content')

    <H1>@lang('Registrerad tid')</H1>

    @if(count($workplaces) > 0)
        <ul class="list-group mb-3 tracks">
            @foreach($workplaces as $workplace)
                @if(count($workplace->project_times) > 0)
                    @foreach($workplace->project_times->sortBy('date') as $project_time)
                        <li class="list-group-item d-flex justify-content-between lh-condensed nopadding">
                                <a href="/projecttime/{{$project_time->id}}/edit">
                                <h6 class="my-0">{{$project_time->date}} {{$project_time->startstr()}}-{{$project_time->endstr()}} ({{$workplace->name}})</h6>
                            </a>
                        </li>
                    @endforeach
                @endif
            @endforeach
        </ul>
    @endif

@endsection
