@extends('layouts.app')

@section('title', __('Testresultat'))

@section('content')

    <H1>@lang('Testresultat')</H1>

    @lang('Grattis, du hade rätt på :percent% av frågorna på första försöket!', ['percent' => $test_session->percent()])

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

    <div>
        <img id="upvote" class="votebutton" src="/images/upvote.png">
        <img id="downvote" class="votebutton" src="/images/downvote.png">
    </div>

    <script type="text/javascript">
        $(document).ready(function(){
            $("#upvote").click(function(){
                var lessonId = "{{$test_session->lesson->id}}";
                var userId = "{{$test_session->user->id}}";
                var token = "{{ csrf_token() }}";
                var vote = "1";
                $.ajax({
                    url: '/lessons/'+lessonId+'/vote',
                    data : {_token:token,user_id:userId,vote:vote},
                    type: 'PUT'
                });
            });
            $("#downvote").click(function(){
                var lessonId = "{{$test_session->lesson->id}}";
                var userId = "{{$test_session->user->id}}";
                var token = "{{ csrf_token() }}";
                var vote = "-1";
                $.ajax({
                    url: '/lessons/'+lessonId+'/vote',
                    data : {_token:token,user_id:userId,vote:vote},
                    type: 'PUT'
                });
            });
        });
    </script>

@endsection
