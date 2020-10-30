@extends('layouts.app')

@section('title', __('Test'))

@section('content')

    <script type="text/javascript">
        function enableordisableall(enabled, mr, mr_length) {
            for(var i=0; i < mr_length; i++) {
                if(!mr[i].checked) {
                    mr[i].disabled = !enabled;
                }
            }
        }

        function chkcontrol(j) {
            var total=0;
            var mr=document.getElementsByName("multiresponse[]");
            var mr_length=mr.length;
            for(var i=0; i < mr_length; i++) {
                if(mr[i].checked) {
                    total=total+1;
                }
                if(total < {{$question->correctAnswers}}){
                    document.question.submit.disabled = true;
                    enableordisableall(true, mr, mr_length);
                } else {
                    document.question.submit.disabled = false;
                    enableordisableall(false, mr, mr_length);
                }
            }
        }
    </script>

    <H1>@lang('Fråga :question av :questions', ['question' => $question_number, 'questions' => $number_of_questions])</H1>

    {{$question->translateOrDefault(App::getLocale())->text}}

    <br><br>

    <form method="post" name="question" action="{{action('TestController@store')}}" accept-charset="UTF-8">
        @csrf

        @if(count($question->response_options) == 0)
            <button class="btn btn-primary btn-lg btn-block" id="submit" name="submit" type="submit">@lang('Gå vidare')</button>
        @else

            @if ($question->correctAnswers < 2)
                @foreach($question->response_options as $response_option)
                    <div class="radio">
                        <label><input type="radio" name="singleresponse" value="{{$response_option->id}}" onclick="document.question.submit.disabled=false;">{{$response_option->translateOrDefault(App::getLocale())->text}}</label>
                    </div>
                @endforeach
            @else
                <p>@lang('(Ange :alternatives alternativ)', ['alternatives' => $question->correctAnswers])</p>
                @foreach($question->response_options as $response_option)
                    <div class="checkbox">
                        <label><input type="checkbox" name="multiresponse[]" value="{{$response_option->id}}" id="{{$response_option->id}}" onclick="chkcontrol({{$response_option->id}})">{{$response_option->translateOrDefault(App::getLocale())->text}}</label>
                    </div>
                @endforeach
            @endif

            <br><br>

            <button class="btn btn-primary btn-lg btn-block" id="submit" name="submit" type="submit" disabled>@lang('Gå vidare')</button>

        @endif

    </form>

@endsection
