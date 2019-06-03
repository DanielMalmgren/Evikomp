@extends('layouts.app')

@section('content')

<script src="/trumbowyg/trumbowyg.min.js"></script>
<script type="text/javascript" src="/trumbowyg/langs/sv.min.js"></script>

<script type="text/javascript">
    $(function() {
        $('#bodytext').trumbowyg({
            btns: [
                ['formatting'],
                ['strong', 'em', 'del'],
                ['link'],
                ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                ['unorderedList', 'orderedList']
            ],
            lang: 'sv',
            removeformatPasted: true,
            minimalLinks: true
        });
    });
</script>

<div class="col-md-6">

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
