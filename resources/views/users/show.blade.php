@extends('layouts.app')

@section('title', __('Användarstatistik'))

@section('content')

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

    <H1>@lang('Statistik och information för') {{$user->name}}</H1>

    @lang('Antal timmar i plattformen totalt:') {{$total_active_time}} <br><br>

    <H2>@lang('Avklarade lektioner')</H2>
    @foreach($tracks as $track)
        <h3>{{$track->translateOrDefault(App::getLocale())->name}}</h3>
        @foreach($track->lessons->sortBy('order') as $lesson)
            {{$lesson->translateOrDefault(App::getLocale())->name}}
            @if($lesson->isFinished())
                <small data-toggle="tooltip" title="@lang('Markerad som färdig')"><i class="fas fa-check"></i></small>
            @endif
            <br>
        @endforeach
        <br>
    @endforeach

    <br><br>

    <a href="/settings/{{$user->id}}" class="btn btn-primary">@lang('Inställningar')</a>

@endsection
