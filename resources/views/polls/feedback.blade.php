
@extends('layouts.app')

@section('title', __('Enkät'))

@section('content')

    <H1>@lang('Tack för din medverkan!')</H1>

    <div class="card">
        <div class="card-body">
            {!!$poll->translateOrDefault(App::getLocale())->infotext2!!}
        </div>
    </div>

    @isset($lesson_result)
        <br><br>
        <a href="/result/{{$lesson_result->lesson_id}}" class="btn btn-primary">@lang('Tillbaka till resultatsidan')</a>
    @endisset

@endsection
