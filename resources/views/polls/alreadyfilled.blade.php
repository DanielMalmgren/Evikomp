
@extends('layouts.app')

@section('title', __('Enkät'))

@section('content')

    <H1>{{$poll->translateOrDefault(App::getLocale())->name}}</H1>

    <div class="card">
        <div class="card-body">
            @lang('Du har redan besvarat denna enkät, det är endast möjligt att besvara den en gång!')
        </div>
    </div>

    <br>

@endsection
