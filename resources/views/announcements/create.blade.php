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

    <H1>@lang('Skapa meddelande')</H1>

    <form method="post" action="{{action('AnnouncementsController@store')}}" accept-charset="UTF-8">
        @csrf

        <div class="mb-5">
            <label for="heading">@lang('Rubrik')</label>
            <input name="heading" class="form-control" id="heading" required>
        </div>

        <div class="mb-5">
            <label for="preamble">@lang('Ingress')</label>
            <input name="preamble" class="form-control" id="preamble" required>
        </div>

        <div class="mb-5">
                <label for="bodytext">@lang('Text')</label>
                <textarea rows=5 name="bodytext" class="form-control" id="bodytext" required></textarea>
            </div>

        <br>

        <button class="btn btn-primary btn-lg btn-block" type="submit">@lang('Skapa')</button>
    </form>
</div>

@endsection
