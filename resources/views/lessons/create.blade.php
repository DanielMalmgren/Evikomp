@extends('layouts.app')

@section('content')

<script src="/trumbowyg/trumbowyg.min.js"></script>
<script type="text/javascript" src="/trumbowyg/langs/sv.min.js"></script>
<script type="text/javascript" language="javascript" src="{{asset('vendor/jquery-ui-1.12.1.custom/jquery-ui.min.js')}}"></script>

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

<x-add-content/>

<script type="text/javascript">
    function addtwe() {
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
    }

    function update_content_order() {
        var order = $("#contents_wrap").sortable("toArray");
        $('#content_order').val(order.join(","));
    }

    $(function() {
        $('#limited_by_title').on('change', function() {
            var val = this.checked;
            $("#titles").toggle(this.checked);
        });

        $("#contents_wrap").sortable({
            update: function (e, u) {
                update_content_order();
            },
            handle: '.handle',
            axis: 'y'
        });
    });
</script>

    <H1>@lang('Lägg till modul')</H1>

    <form method="post" name="lesson" action="{{action('LessonController@store')}}" accept-charset="UTF-8" enctype="multipart/form-data">
        @csrf

        <input type="hidden" id="content_order" name="content_order" value="" />
        <input type="hidden" name="track" value="{{$track->id}}">

        <div class="mb-3">
            <label for="name">@lang('Namn')</label>
            <input name="name" class="form-control" id="name" value="{{old('name')}}">
        </div>

        <div class="mb-3">
            <label for="color">@lang('Färg')</label>
            <input name="color" type="color" list="presetColors">
            <datalist id="presetColors">
                @foreach($colors as $color)
                    <option>{{$color->hex}}</option>
                @endforeach
            </datalist>
        </div>

        <div class="mb-3">
            <label for="icon">@lang('Ikon: ') </label>
            <input name="icon" class="form-control" type="file" accept="image/jpeg,image/png,image/gif">
        </div>

        <div class="mb-3">
            <input type="hidden" name="active" value="0">
            <label><input type="checkbox" name="active" value="1" {{old('active')?"checked":""}}>@lang('Aktiv')</label>
        </div>

        <div class="mb-3">
            <input type="hidden" name="limited_by_title" value="0">
            <label><input type="checkbox" name="limited_by_title" id="limited_by_title" value="1">@lang('Begränsad enbart till vissa befattningar')</label>
        </div>

        <div id="titles" style="display: none;">
            @foreach($titles as $title)
                <label><input type="checkbox" name="titles[]" value="{{$title->id}}">{{$title->workplace_type->name}} - {{$title->name}}</label><br>
            @endforeach
        </div>

        <label>@lang('Notifieringsmottagare')</label>
        <div id="notification_receivers_wrap"></div>

        <br>

        <div id="add_notification_receiver_button" class="btn btn-primary" style="margin-bottom:15px" type="text">@lang('Lägg till notifieringsmottagare')</div>

        <h2>@lang('Innehåll')</h2>
        <div id="contents_wrap"></div>

        <br>

        <div class="row">
            <div class="col-lg-4">
                <label for="locale">@lang('Typ av innehåll att lägga till')</label>
                <select class="custom-select d-block w-100" name="content_to_add" id="content_to_add">
                    <option selected disabled value="select">@lang('Välj typ av innehåll')</option>>
                    <option value="vimeo">@lang('Vimeo-film')</option>
                    <option value="youtube">@lang('Youtube-film')</option>
                    <option value="html">@lang('Text')</option>
                    <option value="audio">@lang('Pod (ljudfil)')</option>
                    <option value="office">@lang('Office-fil (Word, Excel, Powerpoint)')</option>
                    <option value="image">@lang('Bild')</option>
                    <option value="file">@lang('Övrig fil')</option>
                    <option value="pagebreak">@lang('Sidrubrik')</option>
                </select>
            </div>
        </div>

        <br><br>

        <button disabled class="btn btn-primary btn-lg btn-block" name="submit" type="submit">@lang('Spara')</button>
    </form>

<script type="text/javascript">

    function addselect2() {
        $('.new_notification_receivers').select2({
            width: '100%',
            ajax: {
                url: '/select2users',
                dataType: 'json'
            },
            language: "sv",
            minimumInputLength: 3,
            theme: "bootstrap4"
        });
    }

    $(function() {
        var wrapper = $("#notification_receivers_wrap");
        var add_button = $("#add_notification_receiver_button");

        $(add_button).click(function(e){
            e.preventDefault();
            $(wrapper).append('<a class="list-group-item list-group-item-action"><div class="row"><div class="col-lg-9 col-md-9 col-sm-7"><select class="new_notification_receivers" name="new_notification_receivers[]"></select></div><div class="col-lg-1 col-md-3 col-sm-5"><i class="fas fa-trash remove_field"></i></div></div></a>');
            addselect2();
        });

        $(wrapper).on("click",".remove_field", function(e){
            e.preventDefault();
            var parentdiv = $(this).parent('div').parent('div').parent('a');
            parentdiv.remove();
        })

    });
</script>

@endsection
