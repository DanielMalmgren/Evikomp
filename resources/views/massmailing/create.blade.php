
@extends('layouts.app')

@section('title', __('Massutskick'))

@section('content')

    <script src="/trumbowyg/trumbowyg.min.js"></script>
    <script type="text/javascript" src="/trumbowyg/langs/sv.min.js"></script>

    <H1>@lang('Skicka ut massmail')</H1>

    <form method="post" onsubmit="return validate(this);" action="{{action('MassMailingController@store')}}" accept-charset="UTF-8" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="subject">@lang('Rubrik')</label>
            <input name="subject" class="form-control" id="subject">
        </div>

        <div class="mb-3">
            <label for="body">@lang('Meddelandetext')</label>
            <textarea rows="6" name="body" class="form-control twe"></textarea>
        </div>

        @lang('Målgrupp:') <br>
        <select id="workplaces" name="workplaces[]" multiple="multiple">
            @foreach($workplaces as $workplace)
                <option value="{{$workplace->id}}" data-section="{{$workplace->municipality->name}}">{{$workplace->name}}</option>
            @endforeach
        </select>

        <br><br>

        <button class="btn btn-primary btn-lg btn-primary" type="submit">@lang('Skicka')</button>

    </form>

    <script type="text/javascript">
        function validate(form) {
            checked = document.querySelectorAll('input[type="checkbox"]:checked.option').length;
            return confirm("@lang('Detta kommer att skicka e-post till samtliga medarbetare på ')" + checked + "@lang(' arbetsplatser. Är du säker?')");
        }

        $('.twe').trumbowyg({
            btns: [
                ['formatting'],
                ['strong', 'em', 'del'],
                ['link'],
                ['justifyLeft', 'justifyCenter'],
                ['unorderedList', 'orderedList'],
                ['horizontalRule'],
                ['fullscreen']
            ],
            lang: 'sv',
            removeformatPasted: true,
            minimalLinks: true
        });
    </script>

    <link href="/tree-multiselect/jquery.tree-multiselect.min.css" rel="stylesheet">
    <script src="/tree-multiselect/jquery.tree-multiselect.min.js"></script>
    <script type="text/javascript">
    	$("select#workplaces").treeMultiselect({
            startCollapsed: true,
            hideSidePanel: true
        });
    </script>

@endsection
