
@extends('layouts.app')

@section('title', __('Enkät'))

@section('content')

    <H1>{{$poll->translateOrDefault(App::getLocale())->name}}</H1>
    {{$poll->translateOrDefault(App::getLocale())->infotext}}

    @foreach($poll->poll_questions->sortBy('order') as $question)
        <div class="card">
            <div class="card-body">
                @if($question->type == 'pagebreak')
                    <hr>
                @else
                    {{$question->translateOrDefault(App::getLocale())->text}}
                @endif
            </div>
        </div>
    @endforeach

    <br>

    <a href="/poll/{{$poll->id}}/exportresponses" class="btn btn-primary">@lang('Exportera enkätsvar')</a>

@endsection
