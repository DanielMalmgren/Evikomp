@extends('layouts.app')

@section('content')

<div class="col-md-5 mb-3">

    <H1>@lang('Skapa meddelande')</H1>

    <form method="post" action="{{action('AnnouncementsController@store')}}" accept-charset="UTF-8">
        @csrf

        <div class="mb-3">
            <label for="heading">@lang('Rubrik')</label>
            <input name="heading" class="form-control" id="heading">
        </div>

        <div class="mb-3">
                <label for="bodytext">@lang('Text')</label>
                <textarea rows=5 name="bodytext" class="form-control" id="bodytext"></textarea>
            </div>

        <br>

        <button class="btn btn-primary btn-lg btn-block" type="submit">@lang('Skapa')</button>
    </form>
</div>

@endsection
