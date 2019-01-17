@extends('layouts.app')

@section('content')

    <H1>@lang('Nyheter')</H1>

    @if(count($announcements) > 0)
        <ul class="list-group mb-3 announcements">
            @foreach($announcements as $announcement)
                <li class="list-group-item d-flex justify-content-between lh-condensed nopadding">
                    <a href="/announcements/{{$announcement->id}}">
                        <h6 class="my-0">{{$announcement->heading}}</h6>
                        <small class="text-muted">{{$announcement->bodytext}}</small>
                    </a>
                </li>
            @endforeach
        </ul>
    @endif

@endsection
