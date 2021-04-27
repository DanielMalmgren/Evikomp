@extends('layouts.pdfapp')

@section('title', 'Diplom Evikomp')

@section('content')

    <style>
        @page { margin: 0px 0px; }
        body {
            background-image:url({{env('APP_URL').'/images/diploma_background.png'}});
            background-repeat:no-repeat;
            width:100%;
            height:100vh;
            background-size: cover;
            text-align: center;
            color: #000000;
            font-family: Arial Bold;
        }
        .bigcontent {
            margin-top: 400px;
            line-height: 150%;
            font-size: 50px;
        }
        .smallcontent {
            margin-top: 50px;
            line-height: 150%;
            font-size: 25px;
        }
    </style>

    <div class="bigcontent">
        @lang('Detta diplom intygar att')<br>

        {{$name}}<br>

        @lang('framgångsrikt har genomfört')<br>

        @if($lesson->diploma_layout == "module")
            {{$lesson->translateOrDefault(App::getLocale())->name}}<br>
        @else
            {{$lesson->track->translateOrDefault(App::getLocale())->name}}<br>
        @endif
    </div>

    @if($lesson->diploma_layout == "track_module_list")
        <div class="smallcontent">
            @foreach($track_lessons as $track_lesson)
                {{$track_lesson->translateOrDefault(App::getLocale())->name}}<br>
            @endforeach
        </div>
    @endif

@endsection
