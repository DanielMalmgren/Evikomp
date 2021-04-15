@extends('layouts.app')

@section('title', __('Testresultat'))

@section('content')

    <H1>@lang('Testresultat')</H1>

    <div class="card">
        <div class="card-body">

            {{--{{$resulttext}}--}}

            @for ($i = 10; $i <= 100; $i=$i+10)
                @if($percent>=$i)
                    <img class="resultstar" src="/images/Star_happy.png">
                @else
                    <img class="resultstar" src="/images/Star_unhappy.png">
                @endif
            @endfor

            {{--<div>
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
            </div>--}}

            <br><br>

            @if($percent < $lesson->test_required_percent)
                @lang('Inte riktigt alla rätt rakt igenom. Klicka på knappen nedan för att gå tilbaka till modulen och repetera.')
                <br><br>
                <a href="/lessons/{{$lesson->id}}" class="btn btn-primary">@lang('Tillbaka till modulen')</a>
            @elseif(isset($lesson->poll))
                @lang('Du klarade testet. Bra jobbat!')<br>
                @lang('Vi önskar nu att du fyller i en enkät för att utvärdera denna modul.')<br>
                <a href="/poll/{{$lesson->poll->id}}" class="btn btn-primary">@lang('Utvärdera denna modul')</a>        
            @elseif(isset($nextlesson))
                @lang('Bra, du klarade testet! Modulen är nu markerad som färdig.')
            @else
                @lang('Du klarade testet. Bra jobbat!')
            @endif

            <br><br>
            @if(!isset($lesson->poll))
                <a href="/feedback">@lang('Vi vill gärna veta vad du tyckte om modulen. Klicka här för att lämna din åsikt!')</a>
            @endif
            {{--<p>
                @lang('Vad tyckte du om lektionen? Ge tumme upp eller ned. Ditt svar är helt anonymt och hjälper oss att utveckla bättre innehåll!')
            </p>

            <div>
                <img id="upvote" class="votebutton" src="/images/upvote.png">
                <img id="downvote" class="votebutton" src="/images/downvote.png">
            </div>--}}
        </div>
    </div>

    {{--<br>
    @if($nextlesson)
        <h1>@lang('Nästa lektion')</h1>
        @include('inc.listlesson')
        <br>
    @endif--}}

    {{--<script type="text/javascript">
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
    </script>--}}

@endsection
