
@extends('layouts.app')

@section('title', __('Enkät'))

@section('content')

    <H1>{{$poll->translateOrDefault(App::getLocale())->name}}</H1>
    {{$poll->translateOrDefault(App::getLocale())->infotext}}<br><br>

    Aktiverad för följande kommuner: <br>
    @foreach($poll->municipalities as $municipality)
        {{$municipality->name}}<br>
    @endforeach

    mellan {{$poll->active_from}} och {{$poll->active_to}}

    <br><br>

    @foreach($poll->poll_questions->sortBy('order') as $question)
        <div class="card">
            <div class="card-body">
                @if($question->type == 'pagebreak')
                    <hr>
                @else
                    {{$question->translateOrDefault(App::getLocale())->text}} -
                    {{$question->compulsory?"Obligatorisk":"Frivillig"}}
                    @if($question->type == 'freetext')
                        fritextfråga
                    @elseif($question->max_alternatives == 1)
                        envalsfråga med {{count($question->alternatives_array)}} alternativ
                    @else
                        flervalsfråga med {{count($question->alternatives_array)}} alternativ
                        varav man måste välja minst {{$question->min_alternatives}} och max {{$question->max_alternatives}}
                    @endif
                @endif
            </div>
        </div>
    @endforeach

    <br>

    <a href="/poll/{{$poll->id}}/exportresponses" class="btn btn-primary">@lang('Exportera enkätsvar')</a>

@endsection
