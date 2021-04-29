@extends('layouts.app')

@section('title', __('Testresultat'))

@section('content')

    <H1>@lang('Resultat')</H1>

    <div class="card">
        <div class="card-body">

            @if($lesson_has_test)
                @for ($i = 10; $i <= 100; $i=$i+10)
                    @if($percent>=$i)
                        <img class="resultstar" src="/images/Star_happy.png">
                    @else
                        <img class="resultstar" src="/images/Star_unhappy.png">
                    @endif
                @endfor
                <br><br>
            @endif

            @if($percent < $lesson->test_required_percent)
                @lang('Inte riktigt alla rätt rakt igenom. Klicka på knappen nedan för att gå tilbaka till modulen och repetera.')
                <br><br>
                <a href="/lessons/{{$lesson->id}}" class="btn btn-primary">@lang('Tillbaka till modulen')</a>
            @else
                @if($lesson_has_test)
                    @lang('Bra, du klarade testet! Modulen är nu markerad som färdig.')
                @else
                    @lang('Modulen är nu markerad som färdig.')
                @endif
                <br><br>
                @if(isset($lesson->poll))
                    @lang('Vi önskar nu att du fyller i en enkät för att utvärdera denna modul.')<br>
                    <a href="/poll/{{$lesson->poll->id}}" class="btn btn-primary">@lang('Utvärdera denna modul')</a>
                    <br><br>
                @endif
                @if(isset($lesson->diploma_layout))
                    <a href="/testresult/{{$lesson->id}}/pdfdiploma" class="btn btn-primary">@lang('Skriv ut diplom')</a>
                @endif
            @endif

            <br><br>
            @if(!isset($lesson->poll))
                <a href="/feedback">@lang('Vi vill gärna veta vad du tyckte om modulen. Klicka här för att lämna din åsikt!')</a>
            @endif
        </div>
    </div>

@endsection
