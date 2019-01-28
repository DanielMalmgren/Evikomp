@extends('layouts.app')

@section('content')

    <H1>@lang('Redigera lektion')</H1>

    <form method="post" action="{{action('LessonController@update', $lesson->id)}}" accept-charset="UTF-8">
        @method('put')
        @csrf

        <div class="mb-3">
            <label for="name">@lang('Namn')</label>
            <input name="name" class="form-control" id="name" value="{{$lesson->translateOrDefault(App::getLocale())->name}}">
        </div>

        <div class="mb-3">
            <label for="description">@lang('Beskrivning')</label>
            <textarea rows=5 name="description" class="form-control" id="description" value="{{$lesson->translateOrDefault(App::getLocale())->description}}"></textarea>
        </div>

        @if(count($lesson->questions) > 0)
            @lang('Frågor')
            <ul class="list-group mb-3" id="questionlist">
                @foreach($lesson->questions as $question)
                    <li class="list-group-item d-flex justify-content-between lh-condensed">
                        <div>
                        <a href="/test/question/{{$question->id}}/edit">
                            <h6 class="my-0">{{$question->translateOrDefault(App::getLocale())->text}}</h6>
                        </a>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif

        <br>

        <button class="btn btn-primary btn-lg btn-block" type="submit">@lang('Spara')</button>
    </form>

@endsection
