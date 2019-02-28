@extends('layouts.app')

@section('title', $track->translateOrDefault(App::getLocale())->name)

@section('content')

    <H1>{{$track->translateOrDefault(App::getLocale())->name}}</H1>

    @if(count($lessons) > 0)
        <div class="list-group mb-4 lessonslist">
            @foreach($lessons as $lesson)
                @include('inc.listlesson')
            @endforeach
        </div>
    @endif

    @can('manage lessons')
    <a href="/lessons/create/{{$track->id}}" class="btn btn-primary">@lang('Lägg till lektion')</a>
    @endcan

@endsection
