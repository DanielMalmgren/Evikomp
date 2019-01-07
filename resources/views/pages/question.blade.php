@extends('layouts.app')

@section('content')

    <script type="text/javascript">
        function enableordisableall(enabled) {
            var mr=document.getElementsByName("multiresponse[]");
            for(var i=0; i < mr.length; i++) {
                if(!mr[i].checked) {
                    mr[i].disabled = !enabled;
                }
            }
        }

        function chkcontrol(j) {
            var total=0;
            alert(j);
            var mr=document.getElementsByName("multiresponse[]");
            alert(mr.length);
            for(var i=0; i < mr.length; i++) {
                if(mr[i].checked) {
                    total=total+1;
                }
                if(total < {{$question->correctAnswers}}){
                    document.question.submit.disabled = true;
                    enableordisableall(true);
                } else {
                    document.question.submit.disabled = false;
                    enableordisableall(false);
                }
            }
        }
    </script>

    <H1>Fråga {{$question->order}} av {{$test_session->number_of_questions()}}</H1>

    {{$question->translateOrDefault(App::getLocale())->text}}

    <br><br>

    @if(count($response_options) > 0)
        <form method="post" name="question" action="{{action('QuestionController@store')}}" accept-charset="UTF-8">
            @csrf

            {{-- <input type="hidden" name="testsession_id" value="{{$testsession->id}}">
            <input type="hidden" name="question_id" value="{{$question->id}}">
            <input type="hidden" name="test_response_id" value="{{$test_response->id}}"> --}}

            @if ($question->correctAnswers < 2)
                @foreach($response_options as $response_option)
                    <div class="radio">
                        <label><input type="radio" name="singleresponse" value="{{$response_option->id}}" onclick="document.question.submit.disabled=false;">{{$response_option->translateOrDefault(App::getLocale())->text}}</label>
                    </div>
                @endforeach
            @else
                <p>(Ange {{$question->correctAnswers}} alternativ)</p>
                @foreach($response_options as $response_option)
                    <div class="checkbox">
                        <label><input type="checkbox" name="multiresponse[]" value="{{$response_option->id}}" id="{{$response_option->id}}" onclick="chkcontrol({{$response_option->id}})">{{$response_option->translateOrDefault(App::getLocale())->text}}</label>
                    </div>
                @endforeach
            @endif

            <br><br>

            <button class="btn btn-primary btn-lg btn-block" id="submit" name="submit" type="submit" disabled>@lang('Gå vidare')</button>
        </form>
    @endif

@endsection
