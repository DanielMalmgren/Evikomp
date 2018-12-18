@extends('layouts.app')

@section('content')

    <script type="text/javascript">
        function chkcontrol(j) {
            var total=0;
            for(var i=0; i < document.question.response.length; i++) {
                if(document.question.response[i].checked) {
                    total=total+1;
                }
                if(total < {{$question->correctAnswers}}){
                    document.question.submit.disabled = true;
                } else {
                    document.question.submit.disabled = false;
                }
                if(total > {{$question->correctAnswers}}){
                    alert("Du får välja {{$question->correctAnswers}} svarsalternativ!")
                    document.getElementById(j).checked = false;
                    return false;
                }
            }
        }
    </script>

    {{$question->translateOrDefault(App::getLocale())->text}}

    <br><br>

    @if(count($responseoptions) > 0)
        <form method="post" name="question" action="{{action('TestController@store')}}" accept-charset="UTF-8">
            @csrf

            <input type="hidden" name="question_id" value="{{$question->id}}">

            @if (!$question->isMultichoice)
                @foreach($responseoptions as $responseoption)
                    <div class="radio">
                        <label><input type="radio" name="response" value="{{$responseoption->id}}" onclick="document.question.submit.disabled=false;">{{$responseoption->translateOrDefault(App::getLocale())->text}}</label>
                    </div>
                @endforeach
            @else
                <p>(Ange {{$question->correctAnswers}} alternativ)</p>
                @foreach($responseoptions as $responseoption)
                    <div class="checkbox">
                        <label><input type="checkbox" name="response" value="{{$responseoption->id}}" id="{{$responseoption->id}}" onclick="chkcontrol({{$responseoption->id}})">{{$responseoption->translateOrDefault(App::getLocale())->text}}</label>
                    </div>
                @endforeach
            @endif

            <br><br>

            <button class="btn btn-primary btn-lg btn-block" id="submit" name="submit" type="submit" disabled>@lang('Gå vidare')</button>
        </form>
    @endif

@endsection
