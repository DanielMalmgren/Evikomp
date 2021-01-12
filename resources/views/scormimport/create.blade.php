@extends('layouts.app')

@section('content')

    <H1>@lang('Importera SCORM-fil')</H1>

    <form method="post" action="{{action('SCORMImportController@store')}}" accept-charset="UTF-8" enctype="multipart/form-data">
        @csrf

        <input type="hidden" name="track" value="{{$track->id}}">

        <input required name="scormfile" class="form-control original-content" type="file">

        <br><br>

        <button class="btn btn-primary btn-lg btn-block" name="submit" type="submit">@lang('Påbörja import')</button>
    </form>

@endsection
