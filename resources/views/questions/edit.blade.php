@extends('layouts.app')

@section('title', __('Redigera fråga'))

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
            <label for="correctAnswers">@lang('Antal korrekta svar som krävs för att få rätt på frågan')</label>
            <select class="custom-select d-block w-100" name="correctAnswers" id="correctAnswers" required="">
                @for ($i = 0; $i < 10; $i++)
                    <option value="{{$i}}" {{$i==$question->correctAnswers?"selected":""}}>{{$i==0?$i." (".__("Reflektionsfråga").")":$i}}</option>
                @endfor
            </select>
        </div>

        <div id="response_options_div">
            @lang('Svarsalternativ')
            <div id="input_fields_wrap">
            @if(count($question->response_options) > 0)
                @foreach($question->response_options as $response_option)

                    <div id="response_toption[{{$response_option->id}}]" data-id="{{$response_option->id}}" class="card">
                        <div class="card-body">
                            <a href="#" class="close remove_field" data-dismiss="alert" aria-label="close">&times;</a>
                            <input name="response_option_text[{{$response_option->id}}]" class="form-control original-content" value="{{$response_option->translateOrDefault(App::getLocale())->text}}">
                            <label for="response_option_correct[{{$response_option->id}}]">@lang('Är svarsalternativet rätt?')</label>
                            <input type="checkbox" {{$response_option->isCorrectAnswer?"checked":""}} name="response_option_correct[{{$response_option->id}}]" value="{{$response_option->id}}">
                        </div>
                    </div>

                @endforeach
            @endif
            </div>

            <br>
            <div id="add_alternative_button" class="btn btn-primary" style="margin-bottom:15px" type="text">@lang('Lägg till ett svarsalternativ')</div>
        </div>

        <br>

        <button class="btn btn-primary" type="submit">@lang('Spara')</button>
        <a href="/lessons/{{$question->lesson->id}}/editquestions" class="btn btn-primary">@lang('Avbryt')</a>
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
            $(wrapper).append('<div class="card"><div class="card-body"><a href="#" class="close remove_field" data-dismiss="alert" aria-label="close">&times;</a><input name="new_response_option_text['+new_id+']" class="form-control original-content"><label for="new_response_option_correct['+new_id+']">@lang("Är svarsalternativet rätt?")</label><input type="checkbox" name="new_response_option_correct['+new_id+']" value="'+new_id+'"></div></div>');
        });

        $(wrapper).on("click",".remove_field", function(e){
            e.preventDefault();
            var parentdiv = $(this).parent('div');
            var textbox = $(this).parent('div').find('.original-content');
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
