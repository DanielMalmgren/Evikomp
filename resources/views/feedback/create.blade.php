@extends('layouts.app')

@section('title', __('Feedback'))

@section('content')

    <H1>@lang('Skicka feedback')</H1>

    <form method="post" action="{{action('FeedbackController@post')}}" accept-charset="UTF-8">
        @csrf

        <div class="mb-3">
            <label for="content">@lang('Meddelande')</label>
            <textarea rows=5 name="content" class="form-control"></textarea>
        </div>

        <br>

        <button class="btn btn-primary btn-lg btn-block" type="submit">@lang('Skicka')</button>
    </form>

@endsection
