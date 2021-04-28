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
                margin-top: 300px;
            @else
                margin-top: 400px;
            @endif
            line-height: 150%;
            font-size: 35px;
            text-align: center;
        }
        .smallcontent {
            margin-top: 30px;
            margin-left: 180px;
            line-height: 150%;
            font-size: 25px;
        }
        li {
            margin: 0;
            padding: 0px 0 0px 50px;
            list-style: none;
            background-image: url({{env('APP_URL')}}/images/listbullet.png);
            background-repeat: no-repeat;
            background-position: left center;
            background-size: 40px;
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
            <div style="margin-left: 40px">
                @lang('Ingående moduler')
            </div>
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
