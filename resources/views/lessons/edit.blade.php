@extends('layouts.app')

@section('content')

<div class="col-md-5 mb-3">

    <H1>@lang('Redigera lektion')</H1>

    <form method="post" action="{{action('LessonController@update', $lesson->id)}}" accept-charset="UTF-8">
        @method('put')
        @csrf

        <div class="mb-3">
            <label for="name">@lang('Namn')</label>
            <input name="name" class="form-control" id="name" value="{{$lesson->translateOrDefault(App::getLocale())->name}}">
        </div>

        <div class="mb-3">
            <label for="description">@lang('Beskrivning')</label>
            <textarea rows=5 name="description" class="form-control" id="description" value="{{$lesson->translateOrDefault(App::getLocale())->description}}"></textarea>
        </div>

        <br>

        <button class="btn btn-primary btn-lg btn-block" type="submit">@lang('Spara')</button>
    </form>
</div>

@endsection
