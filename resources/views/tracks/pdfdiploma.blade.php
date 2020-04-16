@extends('layouts.pdfapp')

@section('title', $track->translateOrDefault(App::getLocale())->name)

@section('content')

    <style>
        @page { margin: 100px 25px; }
        header { position: fixed; top: -60px; left: 0px; right: 0px; height: 50px; }
        footer { position: fixed; bottom: -60px; left: 0px; right: 0px; height: 50px; }
        .completedlist { margin-left: 100px; }
    </style>

    <center><H1>DIPLOM</H1>

    <H2>Tilldelat {{$user->name}}
    För aktivt deltagande och godkänt genomförande
    av Evikomps utbildning i {{$track->translateOrDefault(App::getLocale())->name}}</H2>
    </center>

    <div class="completedlist">

        Kursmoment:

        @if(count($lessons) > 0)
            <ul>
            @foreach($lessons as $lesson)
                <li class="mb-0">
                    {{$lesson->translateOrDefault(App::getLocale())->name}}
                </li>
            @endforeach
            </ul>
        @endif
    </div>

    <footer>
    Loggor och skit här nere.
    </footer>

@endsection
