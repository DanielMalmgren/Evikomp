@extends('layouts.app')

@section('title', __('Testresultat'))

@section('content')

    <H1>@lang('Testresultat')</H1>

    <div class="card">
        <div class="card-body">

            {{$resulttext}}

            <br><br>

            <div>
                @if($test_session->percent()>49)
                    <img class="resultstar" src="/images/Star_happy.png">
                @else
                    <img class="resultstar" src="/images/Star_unhappy.png">
                @endif
                @if($test_session->percent()>74)
                    <img class="resultstar" src="/images/Star_happy.png">
                @else
                    <img class="resultstar" src="/images/Star_unhappy.png">
                @endif
                @if($test_session->percent()==100)
                    <img class="resultstar" src="/images/Star_happy.png">
                @else
                    <img class="resultstar" src="/images/Star_unhappy.png">
                @endif
            </div>

            <br><br>
            <p>
                @lang('Vad tyckte du om lektionen? Ge tumme upp eller ned. Ditt svar är helt anonymt och hjälper oss att utveckla bättre innehåll!')
            </p>

            <div>
                <img id="upvote" class="votebutton" src="/images/upvote.png">
                <img id="downvote" class="votebutton" src="/images/downvote.png">
            </div>
        </div>
    </div>

    <br>
    @if($lesson)
        <h1>@lang('Nästa lektion')</h1>
        @include('inc.listlesson')
        <br>
    @endif

    <script type="text/javascript">
        function vote(vote) {
            var lessonId = "{{$test_session->lesson->id}}";
            var userId = "{{$test_session->user->id}}";
            var token = "{{ csrf_token() }}";
            $.ajax({
                url: '/lessons/'+lessonId+'/vote',
                data : {_token:token,vote:vote},
                type: 'PUT'
            });
            alert("@lang('Tack för din feedback!')");
        }

        $(document).ready(function(){
            $("#upvote").click(function(){
                vote(1);
            });
            $("#downvote").click(function(){
                vote(-1);
            });
        });
    </script>

@endsection
