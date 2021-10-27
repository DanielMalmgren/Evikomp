@extends('layouts.app')

@section('title', __('Användarstatistik'))

@section('content')

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

    <H1>@lang('Statistik och information för') {{$user->name}}</H1>

    <p>@lang('Antal timmar i plattformen hittills: ') {{$totalactivehours}} </p>
    <p>@lang('Antal timmar manuellt registrerade hittills: ') {{$totalprojecthours}}</p>
    <p>@lang('Antal timmar attesterade av deltagare hittills: ') {{$attestedhourslevel1}}</p>
    <p>@lang('Antal timmar attesterade av chef hittills: ') {{$attestedhourslevel3}}</p>
    <br>

    <H2>@lang('Avklarade moduler')</H2>
    @foreach($tracks as $track)
        <h3>{{$track->translateOrDefault(App::getLocale())->name}}</h3>
        @foreach($track->lessons->where('active', true)->sortBy('order') as $lesson)
            {{$lesson->translateOrDefault(App::getLocale())->name}}
            @if($lesson->isFinished($user))
                <small data-toggle="tooltip" title="@lang('Markerad som färdig')"><i class="fas fa-check"></i></small>
            @endif
            <br>
        @endforeach
        <br>
    @endforeach

    <br>

    <a href="/settings/{{$user->id}}" class="btn btn-primary">@lang('Inställningar')</a>

    @canImpersonate()
        <br><br>
        <a href="/users/impersonate/{{$user->id}}" class="btn btn-primary">@lang('Skifta till denna person')</a>
    @endCanImpersonate
    @hasrole('Admin')
        <a href="/log?user={{$user->id}}" class="btn btn-secondary">@lang('Visa logg')</a>
    @endhasrole

@endsection
