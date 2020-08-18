
@extends('layouts.app')

@section('title', __('Enkät'))

@section('content')

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

    <script type="text/javascript">
        $(function() {
            $("#type").change(function() {
                var type=document.getElementById("type");
                switch(type.selectedIndex) {
                case 0: //freetext
                    $("#min_alternatives").hide();
                    $("#max_alternatives").hide();
                    $("#alternatives").hide();
                    $("#compulsory").show();
                    $("#display_criteria").show();
                    break;
                case 1: //select
                    $("#min_alternatives").show();
                    $("#max_alternatives").show();
                    $("#alternatives").show();
                    $("#compulsory").show();
                    $("#display_criteria").show();
                    break;
                case 2: //pagebreak
                    $("#min_alternatives").hide();
                    $("#max_alternatives").hide();
                    $("#alternatives").hide();
                    $("#compulsory").hide();
                    $("#display_criteria").hide();
                    break;
                }
            });


            var wrapper = $("#new_alternatives_wrapper");
            var add_button = $("#add_alternative_button");
            var newindex = 100;

            $(add_button).click(function(e){
                e.preventDefault();
                $(wrapper).append('<div class="row mb-2"><div class="col flex-nowrap"><input name="alternative['+newindex+']" class="form-control"></div><div class="col col-auto flex-nowrap"><i class="fas fa-trash remove_alternative"></i></div></div>');
                newindex++;
            });

            //$(".remove_alternative").click(function(e){
            $('#alternatives').on('click', '.remove_alternative', function(e) {
                e.preventDefault();
                var parentdiv = $(this).parent('div').parent('div');
                parentdiv.remove();
            });

        });
    </script>

    <form method="post" action="{{action('PollQuestionController@store')}}" accept-charset="UTF-8" enctype="multipart/form-data">
        @csrf

        <input type="hidden" name="poll_id" value="{{$poll->id}}">

        <div class="mb-3">
            <label for="text">@lang('Fråga')</label>
            <input name="text" class="form-control" id="text">
        </div>

        <div class="mb-3" id="compulsory">
            <input type="hidden" name="compulsory" value="0">
            <label><input type="checkbox" name="compulsory" value="1">@lang('Obligatorisk')</label>
        </div>

        <div class="mb-3">
            <label for="type">@lang('Typ av fråga')</label>
            <select class="custom-select d-block w-100" name="type" required="" id="type">
                <option value="freetext">@lang('Fritext')</option>
                <option value="select">@lang('Val')</option>
                <option value="pagebreak">@lang('Sidbrytning')</option>
            </select>
        </div>

        <div class="mb-3" style="display:none" id="min_alternatives">
            <label for="min_alternatives">@lang('Minsta antal alternativ att besvara')</label>
            <select class="custom-select d-block w-100" name="min_alternatives" required="">
                @for ($i = 0; $i < 10; $i++)
                    <option value="{{$i}}">{{$i}}</option>
                @endfor
            </select>
        </div>

        <div class="mb-3" style="display:none" id="max_alternatives">
            <label for="max_alternatives">@lang('Högsta antal alternativ att besvara')</label>
            <select class="custom-select d-block w-100" name="max_alternatives" required="">
                @for ($i = 1; $i < 10; $i++)
                    <option value="{{$i}}">{{$i}}</option>
                @endfor
            </select>
        </div>

        <div style="display:none" id="alternatives">
            <label>@lang('Alternativ')</label>
            <div id="new_alternatives_wrapper"></div>
            <div id="add_alternative_button" class="btn btn-primary" style="margin-bottom:15px" type="text">@lang('Lägg till val')</div>
        </div>

        <br>

        <div class="row mb-2 no-gutters" id="display_criteria">
            <div class="col col-auto flex-nowrap pr-1 my-auto">
                <label class="mb-0" for="type">@lang('Visningskriterium: ')</label>
            </div>
            <div class="col flex-nowrap">
                <select class="custom-select" name="display_criteria[0]">
                    <option value="-1">@lang('Inget kriterium')</option>
                    @foreach($other_questions as $other_question)
                        <option value="{{$other_question->id}}">{{$other_question->text}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col col-auto flex-nowrap pr-1 pl-1 my-auto">
                @lang('är')
            </div>
            <div class="col flex-nowrap">
                <input name="display_criteria[1]" class="form-control" value="{{isset($display_criteria_array[1])?$display_criteria_array[1]:""}}">
            </div>
        </div>

        <br>

        <button class="btn btn-primary btn-lg btn-primary" type="submit">@lang('Spara')</button>

    </form>

@endsection
