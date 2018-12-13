@extends('layouts.app')

@section('content')

    {{$question->translateOrDefault(App::getLocale())->text}}

    <br><br>

    @if(count($responseoptions) > 0)
        @if (!$question->isMultichoice)
            @foreach($responseoptions as $responseoption)
                <div class="radio">
                    <label><input type="radio" name="optradio">{{$responseoption->translateOrDefault(App::getLocale())->text}}</label>
                </div>
            @endforeach
        @else
            @foreach($responseoptions as $responseoption)
                <div class="checkbox">
                    <label><input type="checkbox" value="">{{$responseoption->translateOrDefault(App::getLocale())->text}}</label>
                </div>
            @endforeach
        @endif
    @endif

    <br><br>

    <a href="/test/{{$lesson->id}}/{{$question->id+1}}" class="btn btn-primary">@lang('Fortsätt')</a>

@endsection
