@extends('layouts.app')

@section('content')

    <H1>@lang('Redigera fråga')</H1>

    <form method="post" action="{{action('QuestionController@update', $question->id)}}" accept-charset="UTF-8">
        @method('put')
        @csrf

        <div class="mb-3">
            <label for="text">@lang('Fråga')</label>
            <input name="text" class="form-control" id="text" value="{{$question->translateOrDefault(App::getLocale())->text}}">
        </div>

        <div class="mb-3">
            <label for="correctAnswers">@lang('Antal möjliga svar')</label>
            <input name="correctAnswers" class="form-control" id="correctAnswers" value="{{$question->correctAnswers}}">
        </div>

        @if(count($question->response_options) > 0)
            @lang('Svarsalternativ')
            @foreach($question->response_options as $response_option)
                <div class="mb-3">
                    <input name="response_option_text[{{$response_option->id}}]" class="form-control" value="{{$response_option->translateOrDefault(App::getLocale())->text}}">
                    @if($response_option->isCorrectAnswer)
                        <input type="checkbox" checked name="response_option_correct[{{$response_option->id}}]" value="{{$response_option->id}}">
                    @else
                        <input type="checkbox" name="response_option_correct[{{$response_option->id}}]" value="{{$response_option->id}}">
                    @endif
                </div>
            @endforeach
        @endif

        <br>

        <button class="btn btn-primary btn-lg btn-block" type="submit">@lang('Spara')</button>
    </form>

@endsection
