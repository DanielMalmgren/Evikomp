@extends('layouts.app')

@section('content')

    <H1>{{$track->translateOrDefault(App::getLocale())->name}}</H1>

    @if(count($lessons) > 0)
        <ul class="list-group mb-3 lessons">
            @foreach($lessons as $lesson)
                <li class="list-group-item d-flex justify-content-between lh-condensed nopadding">
                    <a href="/lesson/{{$lesson->id}}">
                        <h6 class="my-0">{{$lesson->translateOrDefault(App::getLocale())->name}}</h6>
                        <small class="text-muted">{{$lesson->name}}</small>
                    </a>
                </li>
            @endforeach
        </ul>
    @endif

@endsection
