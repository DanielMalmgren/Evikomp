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

        function chkcontrol() {
            var total=0;
            var mr=document.getElementsByName("multiresponse[]");
            var mr_length=mr.length;
            for(var i=0; i < mr_length; i++) {
                if(mr[i].checked) {
                    total=total+1;
                }
                if(total < {{$question->max_alternatives}}){
                    document.question.submit.disabled = true;
                    enableordisableall(true, mr, mr_length);
                } else {
                    document.question.submit.disabled = false;
                    enableordisableall(false, mr, mr_length);
                }
            }
        }
    </script>

    <H1>@lang('Fråga :question av :questions', ['question' => $question->order, 'questions' => $question->poll->poll_questions->count()])</H1>

    {{$question->translateOrDefault(App::getLocale())->text}}
    
    <br>

    <form method="post" name="question" action="{{action('PollResponseController@store')}}" accept-charset="UTF-8">
        @csrf

        <input type="hidden" name="poll_question_id" value="{{$question->id}}">
        {{--<input type="hidden" name="poll_session_id" value="{{$question->id}}">--}}
        <input type="hidden" name="poll_id" value="{{$question->poll->id}}">

        @if($question->type == "freetext")
            <textarea rows={{$question->max_alternatives}} name="response" class="form-control" id="response"  oninput="document.question.submit.disabled=false;" required></textarea>
        @elseif($question->type == "select")
            @if ($question->max_alternatives < 2)
                @foreach($question->alternatives_array as $alternative)
                    <div class="radio">
                        <label><input type="radio" name="singleresponse" value="{{$alternative}}" onclick="document.question.submit.disabled=false;">{{$alternative}}</label>
                    </div>
                @endforeach
            @else
                <p>@lang('(Ange max :alternatives alternativ)', ['alternatives' => $question->max_alternatives])</p>
                @foreach($question->alternatives_array as $alternative)
                    <div class="checkbox">
                        <label><input type="checkbox" name="multiresponse[]" value="{{$alternative}}" onclick="chkcontrol()">{{$alternative}}</label>
                    </div>
                @endforeach
            @endif
        @endif

        <br><br>

        <button class="btn btn-primary btn-lg btn-block" id="submit" name="submit" type="submit" disabled>@lang('Gå vidare')</button>

    </form>

@endsection
