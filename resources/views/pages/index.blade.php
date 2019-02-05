@extends('layouts.app')

@section('content')

<H1>@lang('Nyheter')</H1>

@if(count($announcements) > 0)
    <ul class="list-group mb-3 announcements">
        @foreach($announcements as $announcement)
            <li class="list-group-item d-flex justify-content-between lh-condensed">
                <div>
                    <h5 class="my-0">{{$announcement->heading}}</h5>
                    <small class="text-muted">{{$announcement->bodytext}}</small>
                </div>
                <div>{{\Carbon\Carbon::parse($announcement->created_at)->format('Y-m-d')}}<div>
            </li>
        @endforeach
    </ul>
@endif

@can('manage announcements')
    <a href="/announcements/create" class="btn btn-primary">@lang('Skapa nytt meddelande')</a>
@endcan

Aktiv tid idag: {{$active_time}}

@endsection
