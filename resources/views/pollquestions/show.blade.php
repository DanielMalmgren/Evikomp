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
    </script>

    <form method="post" name="question" action="{{action('PollResponseController@store')}}" accept-charset="UTF-8">
        @csrf

        {{--<input type="hidden" name="poll_session_id" value="{{$question->id}}">--}}
        <input type="hidden" name="poll_id" value="{{$question->poll->id}}">

        @while(isset($question))

            @if($question->type == "pagebreak")
                <input type="hidden" name="page_break_id" value="{{$question->id}}">
                @break
            @endif

            <div name="question_{{$question->id}}" {{empty($question->display_criteria)?"":"hidden data-display-criteria=".$question->display_criteria}}>
                {{--<H1>@lang('Fråga :question av :questions', ['question' => $question->order, 'questions' => $question->poll->poll_questions->count()])</H1>--}}

                {{$question->translateOrDefault(App::getLocale())->text}}

                @if(!empty($question->display_criteria))
                    (Denna fråga visas bara om {{$question->display_criteria}})
                @endif

                @if($question->type == "freetext")
                    <textarea rows={{$question->max_alternatives}} name="response[{{$question->id}}]" class="form-control" id="response"  oninput="document.question.submit.disabled=false;" {{$question->compulsory?"required":""}}></textarea>
                @elseif($question->type == "select")
                    @if ($question->max_alternatives < 2)
                        @foreach($question->alternatives_array as $alternative)
                            <div class="radio">
                                <label><input type="radio" name="response[{{$question->id}}]" value="{{$alternative}}" onclick="document.question.submit.disabled=false;">{{$alternative}}</label>
                            </div>
                        @endforeach
                    @else
                        <p>@lang('(Ange max :alternatives alternativ)', ['alternatives' => $question->max_alternatives])</p>
                        @foreach($question->alternatives_array as $alternative)
                            <div class="checkbox">
                                <label><input type="checkbox" name="response[{{$question->id}}][]" value="{{$alternative}}" onclick="chkcontrol({{$question->id}})">{{$alternative}}</label>
                            </div>
                        @endforeach
                    @endif
                @endif

            </div>

            @php
                $question = $question->next_question();
            @endphp

        @endwhile

        <button class="btn btn-primary btn-lg btn-block" id="submit" name="submit" type="submit">@lang('Gå vidare')</button>

    </form>

@endsection
