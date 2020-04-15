@extends('layouts.pdfapp')

@section('title', $track->translateOrDefault(App::getLocale())->name)

@section('content')


    <center><H1>DIPLOM</H1>

    <H2>Tilldelat {{$user->name}}
    För aktivt deltagande och godkänt genomförande
    av Evikomps utbildning i {{$track->translateOrDefault(App::getLocale())->name}}</H2>
    </center>

    Kursmoment:

    @if(count($lessons) > 0)
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
        <div class="list-group mb-4 lessonslist" id="lessonslist">
            @foreach($lessons as $lesson)
                <h5 class="mb-0">
                    {{$lesson->translateOrDefault(App::getLocale())->name}}
                </h5>
            @endforeach
        </div>
    @endif

    <br><br><br>
    Loggor och skit här nere.

@endsection
