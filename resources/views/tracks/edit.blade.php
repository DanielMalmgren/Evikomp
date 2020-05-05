@extends('layouts.app')

@section('content')

<div class="col-md-6">

    <H1>@lang('Redigera spår')</H1>

    <form method="post" action="{{action('TrackController@update', $track->id)}}" accept-charset="UTF-8">
        @method('put')
        @csrf

        <div class="mb-3">
            <label for="name">@lang('Namn')</label>
            <input name="name" class="form-control" id="name" value="{{$track->translateOrDefault(App::getLocale())->name}}">
        </div>

        <div class="mb-3">
            <label for="subtitle">@lang('Undertitel')</label>
            <input name="subtitle" class="form-control" id="subtitle" value="{{$track->translateOrDefault(App::getLocale())->subtitle}}">
        </div>

        <div class="mb-3">
            <input type="hidden" name="active" value="0">
            <label><input type="checkbox" name="active" value="1" {{$track->active?"checked":""}}>@lang('Aktiv')</label>
        </div>

        <br>

        <button class="btn btn-primary btn-lg btn-block" type="submit">@lang('Spara')</button>
    </form>
</div>

@endsection
