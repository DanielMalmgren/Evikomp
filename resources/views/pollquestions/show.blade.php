@extends('layouts.app')

@section('title', __('Enkät'))

@section('content')

    <script type="text/javascript">
        function enableordisableall(enabled, mr, mr_length) {
            for(var i=0; i < mr_length; i++) {
                if(!mr[i].checked) {
                    mr[i].disabled = !enabled;
                }
            }
        }

        function chkcontrol(question_id) {
            var total=0;
            var mr=document.getElementsByName("response["+question_id+"][]");
            var mr_length=mr.length;
            for(var i=0; i < mr_length; i++) {
                if(mr[i].checked) {
                    total=total+1;
                }
                if(total < {{$question->max_alternatives}}){
                    //document.question.submit.disabled = true;
                    enableordisableall(true, mr, mr_length);
                } else {
                    //document.question.submit.disabled = false;
                    enableordisableall(false, mr, mr_length);
                }
            }
        }

        $(function() {
            $("form[name='question'] input[type='radio'],form[name='question'] input[type='checkbox']").change(function() { 
                var select = $(this);
                var selectWrapper = $(this).parents("div.question");

                if (select.attr("type") == "checkbox") {
                    var selectedValues=[];
                    selectWrapper.find('input:checked').each(function() {
                        selectedValues.push($(this).val());
                    });
                } else {
                    var selectedValues = [select.val()];
                }

                $( "form[name='question'] div[data-display-criteria]" ).each(function () { 
                    var fieldId = $(this).data("display-criteria").split('==')[0];
                    var fieldValue = $(this).data("display-criteria").split('==')[1];
                    if (selectWrapper.data("id") == fieldId) {
                        if (selectedValues.includes(fieldValue)) {
                            $(this).show();
                            $(this).find("input, textarea").each(function () { 
                                $(this).attr("name", $(this).data("original-name"));
                            }); 
                        } else {
                            $(this).hide();
                            $(this).find("input, textarea").each(function () { 
                                $(this).attr("name", "do_not_save");
                            }); 
                        }
                    }
                });
            });
        });

        $(function() {
            $("form[name='question']").submit(function(e){
                $("form[name='question'] div[data-min-select]").each(function () { 
                    if ($(this).find('input:checked').length < $(this).data("min-select") ) {
                        e.preventDefault();
                        alert("Minst " + $(this).data("min-select") + " alternativ måste väljas");
                        return;
                    }
                });
                /*$( "form[name='question'] div[data-max-select]").each(function () { 
                    if ($(this).find('input:checked').length > $(this).data("max-select") ) {
                        e.preventDefault();
                        alert("Max " + $(this).data("max-select") + " alternativ får väljas");
                        return;
                    }
                });*/
            });
        });
    </script>

    Previous: {{$previous_id}}

    <form method="post" name="question" action="{{action('PollResponseController@store')}}" accept-charset="UTF-8">
        @csrf

        {{--<input type="hidden" name="poll_session_id" value="{{$question->id}}">--}}
        <input type="hidden" name="poll_id" value="{{$question->poll->id}}">
        <input type="hidden" name="previous_id" value="{{$previous_id}}">

        @while(isset($question))

            @if($question->type == "pagebreak")
                <input type="hidden" name="page_break_id" value="{{$question->id}}">
                @break
            @endif

            <div class="question question_{{$question->id}}" data-min-select="{{$question->max_alternatives}}" data-id="{{$question->id}}" {{empty($question->display_criteria)?"":'style=display:none data-display-criteria='.$question->display_criteria}}>
                {{--<H1>@lang('Fråga :question av :questions', ['question' => $question->order, 'questions' => $question->poll->poll_questions->count()])</H1>--}}

                {{$question->translateOrDefault(App::getLocale())->text}}
                @if($question->compulsory)
                    <span class="red">*</span>
                @endif

                @if($question->type == "freetext")
                    <textarea rows={{$question->max_alternatives}} data-original-name="response[{{$question->id}}]" name="response[{{$question->id}}]" class="form-control" id="response"  oninput="document.question.submit.disabled=false;" {{$question->compulsory?"required":""}}></textarea>
                @elseif($question->type == "select")
                    @if ($question->max_alternatives < 2)
                        @foreach($question->alternatives_array as $alternative)
                            <div class="radio">
                                <label><input type="radio" data-original-name="response[{{$question->id}}]" name="response[{{$question->id}}]" value="{{$alternative}}" {{$question->compulsory?"required":""}} onclick="document.question.submit.disabled=false;">{{$alternative}}</label>
                            </div>
                        @endforeach
                    @else
                        <p>@lang('(Ange max :alternatives alternativ)', ['alternatives' => $question->max_alternatives])</p>
                        @foreach($question->alternatives_array as $alternative)
                            <div class="checkbox">
                                <label><input type="checkbox" data-original-name="response[{{$question->id}}]" name="response[{{$question->id}}][]" value="{{$alternative}}" onclick="chkcontrol({{$question->id}})">{{$alternative}}</label>
                            </div>
                        @endforeach
                    @endif
                @endif

            </div>

            @php
                $question = $question->next_question();
            @endphp

        @endwhile

        @isset($previous_id)
            <button class="btn btn-primary btn-lg" formnovalidate value="previous" id="submit" name="submit" type="submit">@lang('Föregående')</button>
        @else
            <button class="btn btn-primary btn-lg" disabled id="submit" name="submit" type="submit">@lang('Föregående')</button>
        @endif
        @if(isset($question))
            <button class="btn btn-primary btn-lg" value="next" id="submit" name="submit" type="submit">@lang('Nästa')</button>
        @else
            <button class="btn btn-primary btn-lg" value="finish" id="submit" name="submit" type="submit">@lang('Avsluta')</button>
        @endif

    </form>

@endsection
