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
            <label for="correctAnswers">@lang('Antal korrekta svar som krävs för att få rätt på frågan')</label>
            <select class="custom-select d-block w-100" name="correctAnswers" id="correctAnswers" required="">
                <option value="0">0 (@lang('Reflektionsfråga'))</option>
                <option value="1" selected>1</option>
                @for ($i = 2; $i < 10; $i++)
                    <option value="{{$i}}">{{$i}}</option>
                @endfor
            </select>
        </div>

        <div class="mb-3">
            <label for="reasoning">@lang('Resonemang')</label>
            <input name="reasoning" class="form-control" id="reasoning">
        </div>

        <div id="response_options_div">
            @lang('Svarsalternativ')
            <div id="input_fields_wrap">
            </div>

            <br>
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
            //$(wrapper).append('<div class="mb-3"><input name="new_response_option_text['+new_id+']" class="form-control"><input type="checkbox" name="new_response_option_correct['+new_id+']" value="'+new_id+'"><button class="btn btn-default btn-danger remove_field" type="button">X</button></div>');
            $(wrapper).append('<div class="card"><div class="card-body"><a href="#" class="close remove_field" data-dismiss="alert" aria-label="close">&times;</a><input name="new_response_option_text['+new_id+']" class="form-control original-content"><label for="new_response_option_correct['+new_id+']">@lang("Är svarsalternativet rätt?")</label><input type="checkbox" name="new_response_option_correct['+new_id+']" value="'+new_id+'"></div></div>');
        });

        $(wrapper).on("click",".remove_field", function(e){
            e.preventDefault();
            var parentdiv = $(this).parent('div');
            parentdiv.remove();
        })

        $('#correctAnswers').on('change', function() {
            var val = $(this).val();
            if(val == 0) {
                $('#response_options_div').hide();
            } else {
                $('#response_options_div').show();
            }
        });
    });
</script>

@endsection
