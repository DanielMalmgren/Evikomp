@extends('layouts.app')

@section('content')

<x-trumbowyg-includes/>

<script type="text/javascript">
    $(function() {
        $('#bodytext').trumbowyg({
            <x-trumbowyg-settings/>
        });
    });
</script>

<div class="col-md-8">

    <H1>@lang('Redigera meddelande')</H1>

    <form method="post" action="{{action('AnnouncementsController@update', $announcement->id)}}" accept-charset="UTF-8">
        @method('put')
        @csrf

        <div class="mb-5">
            <label for="heading">@lang('Rubrik')</label>
            <input name="heading" class="form-control" id="heading" value="{{$announcement->heading}}">
        </div>

        <div class="mb-5">
            <label for="preamble">@lang('Ingress')</label>
            <input name="preamble" class="form-control" id="preamble" value="{{$announcement->preamble}}">
        </div>

        <div class="mb-5">
            <label for="bodytext">@lang('Text')</label>
            <textarea rows=5 name="bodytext" class="form-control" id="bodytext">{{$announcement->bodytext}}</textarea>
        </div>

        <br>

        <button class="btn btn-primary btn-lg btn-block" type="submit">@lang('Spara')</button>
    </form>
</div>

@endsection
