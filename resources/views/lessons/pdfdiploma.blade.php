@extends('layouts.pdfapp')

@section('title', 'Diplom Evikomp')

@section('content')

    <style>
        @page { margin: 0px 0px; }
        @font-face {
            font-family: "Bodoni italic";
            src: url({{env('APP_URL')}}/fonts/BodoniFLF-Italic.ttf) format("truetype");
        }
        body {
            background-image:url({{env('APP_URL').'/images/diploma_background.png'}});
            background-repeat:no-repeat;
            width:100%;
            height:100vh;
            background-size: cover;
            color: #000000;
            font-family: "Bodoni italic";
        }
        .bigcontent {
            @if($lesson->diploma_layout == "track_module_list")
                margin-top: 350px;
            @else
                margin-top: 400px;
            @endif
            line-height: 120%;
            font-size: 35px;
            text-align: center;
        }
        .smallcontent {
            margin-top: 30px;
            margin-left: 160px;
            line-height: 130%;
            font-size: 25px;
        }
        li {
            margin: 0;
            padding: 0px 0 0px 55px;
            list-style: none;
            background-image: url({{env('APP_URL')}}/images/listbullet.png);
            background-repeat: no-repeat;
            background-position: 0 12;
            background-size: 30px;
        }
        ul {
            padding-inline-start: 0px;
            margin-block-start: 0px;
        }
    </style>

    <div class="bigcontent">
        @lang('Detta diplom intygar att')<br>

        {{$name}}<br>

        @lang('framgångsrikt har genomfört')<br>

        @if($lesson->diploma_layout == "lesson")
            {{$lesson->translateOrDefault(App::getLocale())->name}}<br>
        @else
            {{$lesson->track->translateOrDefault(App::getLocale())->name}}<br>
        @endif
    </div>

    @if($lesson->diploma_layout == "track_module_list")
        <div class="smallcontent">
            <p style="margin-left: 40px; margin-bottom: -20px;">
                @lang('Ingående moduler')
            </p>
            <ul>
                @foreach($track_lessons as $track_lesson)
                    <li>
                        {{$track_lesson->translateOrDefault(App::getLocale())->name}}<br>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

@endsection
