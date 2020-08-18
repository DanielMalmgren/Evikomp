
@extends('layouts.app')

@section('title', __('Enkät'))

@section('content')

    <H1>@lang('Tack för din medverkan!')</H1>

    <div class="card">
        <div class="card-body">
            {!!$poll->translateOrDefault(App::getLocale())->infotext2!!}
        </div>
    </div>


@endsection
