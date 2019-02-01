@extends('layouts.app')

@section('content')

<div class="col-md-5 mb-3">

    <H1>@lang('Skapa ny fråga')</H1>

    <form method="post" action="{{action('QuestionController@store')}}" accept-charset="UTF-8">
        @csrf

        <input type="hidden" name="lesson_id" value="{{$lesson_id}}">

        <div class="mb-3">
            <label for="text">@lang('Fråga')</label>
            <input name="text" class="form-control" id="text">
        </div>

        <div class="mb-3">
            <label for="correctAnswers">@lang('Antal möjliga svar')</label>
            <select class="custom-select d-block w-100" name="correctAnswers" id="correctAnswers" required="">
                @for ($i = 1; $i < 10; $i++)
                    <option value="{{$i}}">{{$i}}</option>
                @endfor
            </select>
        </div>

        @lang('Svarsalternativ')
        <div id="input_fields_wrap">
        </div>

        <div id="add_alternative_button" class="btn btn-primary" style="margin-bottom:15px" type="text">@lang('Lägg till ett svarsalternativ')</div>

        <br>

        <button class="btn btn-primary btn-lg btn-block" type="submit">@lang('Spara')</button>
    </form>

    <script>
        $(document).ready(function() {
            var wrapper = $("#input_fields_wrap");
            var add_button = $("#add_alternative_button");
            var new_id = 0;

            $(add_button).click(function(e){
                e.preventDefault();
                new_id++;
                $(wrapper).append('<div class="mb-3"><input name="new_response_option_text['+new_id+']" class="form-control"><input type="checkbox" name="new_response_option_correct['+new_id+']" value="'+new_id+'"><button class="btn btn-default btn-danger remove_field" type="button">X</button></div>');
            });

            $(wrapper).on("click",".remove_field", function(e){
                e.preventDefault();
                var parentdiv = $(this).parent('div');
                parentdiv.remove();
            })
        });
    </script>

</div>

@endsection
