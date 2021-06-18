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

                @if($passed_now)
                    @lang('Bra, du klarade testet!')<br>
                    @if(!$poll_compulsory)
                        @lang('Modulen är nu markerad som färdig.')
                    @endif
                    <br><br>
                @elseif($passed_earlier)
                    @lang('Du har sedan tidigare klarat testet för denna modul. Du kan dock göra om testet om du vill genom att klicka nedan.')
                    <br><br>
                    <a href="/test/{{$lesson->id}}" class="btn btn-primary">@lang('Gå till testet')</a>
                    <br><br>
                @else
                    @lang('Det verkar som att du behöver läsa på mer. Klicka på knappen nedan för att gå tilbaka till modulen och repetera.')
                    <br><br>
                    <a href="/lessons/{{$lesson->id}}" class="btn btn-primary">@lang('Tillbaka till modulen')</a>
                @endif

            @endif

            @if(!$failed)
                @if($poll_compulsory)
                    @lang('Innan du är helt färdig med modulen vill vi ha din feedback på innehållet. Klicka nedan!')
                    <br>
                    @lang('Observera att modulen inte markeras som färdig förrän du har avslutat enkäten!')
                    <br><br>
                    <a href="/poll/{{$poll->id}}/{{$lesson->id}}" class="btn btn-primary">@lang('Gå till enkät')</a>
                @else
                    @if(!$lesson_has_test)
                        @lang('Modulen är nu markerad som färdig.')
                        <br><br>
                    @endif
                    @if(isset($poll))
                        @lang('Vi uppskattar om du vill fylla i en enkät för att utvärdera denna modul.')
                        <br><br>
                        <a href="/poll/{{$poll->id}}/{{$lesson->id}}" class="btn btn-primary">@lang('Utvärdera denna modul')</a>
                        <br><br>
                    @endif
                    @if(isset($lesson->diploma_layout))
                        <a href="/testresult/{{$lesson->id}}/pdfdiploma" class="btn btn-primary">@lang('Skriv ut diplom')</a>
                    @endif
                @endif
            @endif
        </div>
    </div>

@endsection
