@extends('layouts.app')

@section('content')

<script src="/trumbowyg/trumbowyg.min.js"></script>
<script type="text/javascript" src="/trumbowyg/langs/sv.min.js"></script>
<script type="text/javascript" language="javascript" src="{{asset('vendor/jquery-ui-1.12.1.custom/jquery-ui.min.js')}}"></script>

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

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

    {{-- TODO: One day I will do this function in a prettier way. Not today though, this works.--}}
    function getfreeid() {
        for(;;) {
            testnumber = Math.floor((Math.random() * 1000) + 1);
            hit = 0;
            $('#contents_wrap').children().each(function() {
                if($(this).data("id") == testnumber) {
                    hit=1;
                    return false;
                }
            });
            if(hit==0) {
                return testnumber;
            }
        }
    }

    $(function() {
        var wrapper = $("#contents_wrap");
        var add_button = $("#add_content_button");
        var new_id = 0;

        $(content_to_add).change(function(e){
            e.preventDefault();
            new_id = getfreeid();
            switch($("#content_to_add").val()) {
                case 'vimeo':
                    $(wrapper).append('<div id="new_vimeo['+new_id+']" data-id="'+new_id+'" class="card"><div class="card-body"><span class="handle"><i class="fas fa-arrows-alt-v"></i></span><label class="handle" for="new_vimeo['+new_id+']">@lang('Video-ID')</label><a href="#" class="close remove_field" data-dismiss="alert" aria-label="close">&times;</a><input name="new_vimeo['+new_id+']" class="form-control"></div></div>');
                    break;
                case 'youtube':
                    $(wrapper).append('<div id="new_youtube['+new_id+']" data-id="'+new_id+'" class="card"><div class="card-body"><span class="handle"><i class="fas fa-arrows-alt-v"></i></span><label class="handle" for="new_youtube['+new_id+']">@lang('Youtube-film')</label><a href="#" class="close remove_field" data-dismiss="alert" aria-label="close">&times;</a><input name="new_youtube['+new_id+']" class="form-control original-content"></div></div>');
                    break;
                case 'html':
                    $(wrapper).append('<div id="new_html['+new_id+']" data-id="'+new_id+'" class="card"><div class="card-body"><span class="handle"><i class="fas fa-arrows-alt-v"></i></span><label class="handle" for="new_html['+new_id+']">@lang('Text')</label><a href="#" class="close remove_field" data-dismiss="alert" aria-label="close">&times;</a><textarea rows=5 name="new_html['+new_id+']" class="form-control twe"></textarea></div></div>');
                    addtwe();
                    break;
                case 'audio':
                    $(wrapper).append('<div id="new_audio['+new_id+']" data-id="'+new_id+'" class="card"><div class="card-body"><span class="handle"><i class="fas fa-arrows-alt-v"></i></span><label class="handle" for="new_audio['+new_id+']">@lang('Pod (ljudfil)')</label><a href="#" class="close remove_field" data-dismiss="alert" aria-label="close">&times;</a><input name="new_audio['+new_id+']" class="form-control" type="file" accept="audio/mpeg"></div></div>');
                    break;
                case 'office':
                    $(wrapper).append('<div id="new_office['+new_id+']" data-id="'+new_id+'" class="card"><div class="card-body"><span class="handle"><i class="fas fa-arrows-alt-v"></i></span><label class="handle" for="new_office['+new_id+']">@lang('Office-fil (Word, Excel, Powerpoint)')</label><a href="#" class="close remove_field" data-dismiss="alert" aria-label="close">&times;</a><input name="new_office['+new_id+']" class="form-control" type="file" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.openxmlformats-officedocument.presentationml.presentation"></div></div>');
                    break;
                case 'image':
                    $(wrapper).append('<div id="new_image['+new_id+']" data-id="'+new_id+'" class="card"><div class="card-body"><span class="handle"><i class="fas fa-arrows-alt-v"></i></span><label class="handle" for="new_image['+new_id+']">@lang('Bild')</label><a href="#" class="close remove_field" data-dismiss="alert" aria-label="close">&times;</a><input name="new_image['+new_id+']" class="form-control" type="file" accept="image/jpeg,image/png,image/gif"></div></div>');
                    break;
                case 'file':
                    $(wrapper).append('<div id="new_file['+new_id+']" data-id="'+new_id+'" class="card"><div class="card-body"><span class="handle"><i class="fas fa-arrows-alt-v"></i></span><label class="handle" for="new_file['+new_id+']">@lang('Övrig fil')</label><a href="#" class="close remove_field" data-dismiss="alert" aria-label="close">&times;</a><input name="new_file['+new_id+']" class="form-control" type="file"></div></div>');
                    break;
                case 'pagebreak':
                    $(wrapper).append('<div id="pagebreak['+new_id+']" data-id="'+new_id+'" class="card"><div class="card-body"><span class="handle"><i class="fas fa-arrows-alt-v"></i></span><label class="handle" for="pagebreak['+new_id+']">@lang('Sidrubrik')</label><a href="#" class="close remove_field" data-dismiss="alert" aria-label="close">&times;</a><input name="pagebreak['+new_id+']" class="form-control original-content"></div></div>');
                    break;
            }
            document.lesson.submit.disabled = false;
            update_content_order();
            $("#content_to_add").val('select');
        });

        $(wrapper).on("click",".remove_field", function(e){
            e.preventDefault();
            var parentdiv = $(this).parent('div').parent('div');
            var textbox = $(this).parent('div').find('.form-control')
            var oldname = textbox.attr('name');
            parentdiv.hide();
            textbox.attr('name', 'remove_' + oldname);
        })

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

    <H1>@lang('Lägg till lektion')</H1>

    <form method="post" name="lesson" action="{{action('LessonController@store')}}" accept-charset="UTF-8" enctype="multipart/form-data">
        @csrf

        <input type="hidden" id="content_order" name="content_order" value="" />
        <input type="hidden" name="track" value="{{$track->id}}">

        <div class="mb-3">
            <label for="name">@lang('Namn')</label>
            <input name="name" class="form-control" id="name" value="{{old('name')}}">
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

@endsection
