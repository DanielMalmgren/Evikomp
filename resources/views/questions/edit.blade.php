@extends('layouts.app')

@section('content')

<div class="col-md-5 mb-3">

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
            <select class="custom-select d-block w-100" name="correctAnswers" id="correctAnswers" required="">
                @for ($i = 0; $i < 10; $i++)
                    <option value="{{$i}}" {{$i==$question->correctAnswers?"selected":""}}>{{$i==0?$i." (".__("Reflektionsfråga").")":$i}}</option>
                @endfor
            </select>
        </div>

        <div id="response_options_div">
            @lang('Svarsalternativ')
                @if(count($question->response_options) > 0)
                <div id="input_fields_wrap">
                @foreach($question->response_options as $response_option)
                    <div class="mb-3">
                        <input name="response_option_text[{{$response_option->id}}]" id="response{{$response_option->id}}" class="form-control w-100" value="{{$response_option->translateOrDefault(App::getLocale())->text}}">
                        @if($response_option->isCorrectAnswer)
                            <input type="checkbox" checked name="response_option_correct[{{$response_option->id}}]" value="{{$response_option->id}}">
                        @else
                            <input type="checkbox" name="response_option_correct[{{$response_option->id}}]" value="{{$response_option->id}}">
                        @endif
                        <button class="btn btn-default btn-danger remove_field" type="button">X</button>
                    </div>
                @endforeach
                </div>
            @endif

            <div id="add_alternative_button" class="btn btn-primary" style="margin-bottom:15px" type="text">@lang('Lägg till ett svarsalternativ')</div>
        </div>

        <br>

        <button class="btn btn-primary btn-lg btn-block" type="submit">@lang('Spara')</button>
    </form>

</div>

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
            var textbox = parentdiv.children(":first");
            var oldname = textbox.attr('name');
            parentdiv.hide();
            textbox.attr('name', 'remove_' + oldname);
        })

        $('#correctAnswers').on('change', function() {
            var val = $(this).val();
            if(val == 0) {
                $('#response_options_div').hide();
            } else {
                $('#response_options_div').show();
            }
        });
        $('#correctAnswers').change();
    });
</script>

@endsection
