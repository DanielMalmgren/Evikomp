<script type="text/javascript">

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
        console.log(new_id);
        switch($("#content_to_add").val()) {
            case 'vimeo':
                $(wrapper).append(`
                <div id="new_vimeo[`+new_id+`]" data-id="`+new_id+`" class="card">
                    <div class="card-header">
                        <span class="handle"><i class="fas fa-arrows-alt-v"></i></span>
                        <label class="handle" for="new_vimeo[`+new_id+`]">@lang('Video-film')</label>
                        <a href="#" class="close remove_field pl-3" data-dismiss="alert" aria-label="close">&times;</a>
                        <a data-toggle="collapse" href="#body`+new_id+`" id="collapstoggle`+new_id+`">
                            <i class="fas fa-chevron-up float-right text-dark"></i>
                        </a>
                    </div>
                    <div class="collapse multi-collapse show" id="body`+new_id+`">
                        <div class="card-body">
                            <div class="container">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <label for="new_vimeo[`+new_id+`]">@lang('ID')</label>
                                        <input name="new_vimeo[`+new_id+`]" class="form-control original-content" maxlength="10">
                                    </div>
                                    <div class="col-lg-2">
                                        <label for="settings[`+new_id+`][max_width]">@lang('Maxbredd')</label>
                                        <input name="settings[`+new_id+`][max_width]" class="form-control"">
                                    </div>
                                    <div class="col-lg-3">
                                        <label for="settings[`+new_id+`][adjustment]">@lang('Justering')</label>
                                        <select class="custom-select d-block w-100" name="settings[`+new_id+`][adjustment]">
                                            <option value="float-left">@lang('Vänster')</option>
                                            <option value="mx-auto">@lang('Centrerad')</option>
                                            <option value="float-right">@lang('Höger')</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                `);
                break;
            case 'youtube':
                $(wrapper).append(`
                <div id="new_youtube[`+new_id+`]" data-id="`+new_id+`" class="card">
                    <div class="card-header">
                        <span class="handle"><i class="fas fa-arrows-alt-v"></i></span>
                        <label class="handle" for="new_youtube[`+new_id+`]">@lang('Youtube-film')</label>
                        <a href="#" class="close remove_field pl-3" data-dismiss="alert" aria-label="close">&times;</a>
                        <a data-toggle="collapse" href="#body`+new_id+`" id="collapstoggle`+new_id+`">
                            <i class="fas fa-chevron-up float-right text-dark"></i>
                        </a>
                    </div>
                    <div class="collapse multi-collapse show" id="body`+new_id+`">
                        <div class="card-body">
                            <div class="container">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <label for="new_youtube[`+new_id+`]">@lang('ID')</label>
                                        <input name="new_youtube[`+new_id+`]" class="form-control original-content" maxlength="11">
                                    </div>
                                    <div class="col-lg-2">
                                        <label for="settings[`+new_id+`][max_width]">@lang('Maxbredd')</label>
                                        <input name="settings[`+new_id+`][max_width]" class="form-control"">
                                    </div>
                                    <div class="col-lg-3">
                                        <label for="settings[`+new_id+`][adjustment]">@lang('Justering')</label>
                                        <select class="custom-select d-block w-100" name="settings[`+new_id+`][adjustment]">
                                            <option value="float-left">@lang('Vänster')</option>
                                            <option value="mx-auto">@lang('Centrerad')</option>
                                            <option value="float-right">@lang('Höger')</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                `);
                break;
            case 'html':
                $(wrapper).append(`
                <div id="new_html[`+new_id+`]" data-id="`+new_id+`" class="card">
                    <div class="card-header">
                        <span class="handle"><i class="fas fa-arrows-alt-v"></i></span>
                        <label class="handle" for="new_html[`+new_id+`]">@lang('Text')</label>
                        <a href="#" class="close remove_field pl-3" data-dismiss="alert" aria-label="close">&times;</a>
                        <a data-toggle="collapse" href="#body`+new_id+`" id="collapstoggle`+new_id+`">
                            <i class="fas fa-chevron-up float-right text-dark"></i>
                        </a>
                    </div>
                    <div class="collapse multi-collapse show" id="body`+new_id+`">
                        <div class="card-body">
                            <textarea rows=5 name="new_html[`+new_id+`]" class="form-control twe original-content"></textarea>
                        </div>
                    </div>
                </div>
                `);
                addtwe();
                break;
            case 'audio':
                $(wrapper).append(`
                <div id="new_audio[`+new_id+`]" data-id="`+new_id+`" class="card">
                    <div class="card-header">
                        <span class="handle"><i class="fas fa-arrows-alt-v"></i></span>
                        <label class="handle" for="new_audio[`+new_id+`]">@lang('Pod (ljudfil)')</label>
                        <a href="#" class="close remove_field pl-3" data-dismiss="alert" aria-label="close">&times;</a>
                        <a data-toggle="collapse" href="#body`+new_id+`" id="collapstoggle`+new_id+`">
                            <i class="fas fa-chevron-up float-right text-dark"></i>
                        </a>
                    </div>
                    <div class="collapse multi-collapse show" id="body`+new_id+`">
                        <div class="card-body">
                            <input name="new_audio[`+new_id+`]" class="form-control original-content" type="file" accept="audio/mpeg">
                        </div>
                    </div>
                </div>
                `);
                break;
            case 'office':
                $(wrapper).append(`
                <div id="new_office[`+new_id+`]" data-id="`+new_id+`" class="card">
                    <div class="card-header">
                        <span class="handle"><i class="fas fa-arrows-alt-v"></i></span>
                        <label class="handle" for="new_office[`+new_id+`]">@lang('Office-fil (Word, Excel, Powerpoint)')</label>
                        <a href="#" class="close remove_field pl-3" data-dismiss="alert" aria-label="close">&times;</a>
                        <a data-toggle="collapse" href="#body`+new_id+`" id="collapstoggle`+new_id+`">
                            <i class="fas fa-chevron-up float-right text-dark"></i>
                        </a>
                    </div>
                    <div class="collapse multi-collapse show" id="body`+new_id+`">
                        <div class="card-body">
                            <input name="new_office[`+new_id+`]" class="form-control original-content" type="file" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.openxmlformats-officedocument.presentationml.presentation">
                        </div>
                    </div>
                </div>
                `);
                break;
            case 'google':
                $(wrapper).append(`
                <div id="new_google[`+new_id+`]" data-id="`+new_id+`" class="card">
                    <div class="card-header">
                        <span class="handle"><i class="fas fa-arrows-alt-v"></i></span>
                        <label class="handle" for="new_google[`+new_id+`]">@lang('Google-fil')</label>
                        <a href="#" class="close remove_field pl-3" data-dismiss="alert" aria-label="close">&times;</a>
                        <a data-toggle="collapse" href="#body`+new_id+`" id="collapstoggle`+new_id+`">
                            <i class="fas fa-chevron-up float-right text-dark"></i>
                        </a>
                    </div>
                    <div class="collapse multi-collapse show" id="body`+new_id+`">
                        <div class="card-body">
                            <div class="container">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <label for="new_google[`+new_id+`]">@lang('HTML-kod')</label>
                                        <input name="new_google[`+new_id+`]" class="form-control original-content">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                `);
                break;
            case 'image':
                $(wrapper).append(`
                <div id="new_image[`+new_id+`]" data-id="`+new_id+`" class="card">
                    <div class="card-header">
                        <span class="handle"><i class="fas fa-arrows-alt-v"></i></span>
                        <label class="handle" for="new_image[`+new_id+`]">@lang('Bild')</label>
                        <a href="#" class="close remove_field pl-3" data-dismiss="alert" aria-label="close">&times;</a>
                        <a data-toggle="collapse" href="#body`+new_id+`" id="collapstoggle`+new_id+`">
                            <i class="fas fa-chevron-up float-right text-dark"></i>
                        </a>
                    </div>
                    <div class="collapse multi-collapse show" id="body`+new_id+`">
                        <div class="card-body">
                            <div class="container">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <label for="new_image[`+new_id+`]">@lang('Ladda upp fil')</label>
                                        <input name="new_image[`+new_id+`]" class="form-control" type="file" accept="image/jpeg,image/png,image/gif">
                                    </div>
                                    <div class="col-lg-2">
                                        <label for="settings[`+new_id+`][max_width]">@lang('Maxbredd')</label>
                                        <input name="settings[`+new_id+`][max_width]" class="form-control"">
                                    </div>
                                    <div class="col-lg-3">
                                        <label for="settings[`+new_id+`][adjustment]">@lang('Justering')</label>
                                        <select class="custom-select d-block w-100" name="settings[`+new_id+`][adjustment]">
                                            <option value="float-left">@lang('Vänster')</option>
                                            <option value="mx-auto">@lang('Centrerad')</option>
                                            <option value="float-right">@lang('Höger')</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                `);
                break;
            case 'flipcard':
                $(wrapper).append(`
                    <div id="new_flipcard[`+new_id+`]" data-id="`+new_id+`" class="card">
                        <div class="card-header">
                            <span class="handle"><i class="fas fa-arrows-alt-v"></i></span>
                            <label class="handle">@lang('Vändkort')</label>
                        <a href="#" class="close remove_field pl-3" data-dismiss="alert" aria-label="close">&times;</a>
                        <a data-toggle="collapse" href="#body`+new_id+`" id="collapstoggle`+new_id+`">
                            <i class="fas fa-chevron-up float-right text-dark"></i>
                        </a>
                    </div>
                    <div class="collapse multi-collapse show" id="body`+new_id+`">
                        <div class="card-body">
                            <div class="container">
                                <div class="row">
                                    <div class="col-lg-2">
                                        <label for="content_colors[`+new_id+`]">@lang('Färg')</label>
                                        <input name="content_colors[`+new_id+`]" style="height:35px" type="color" class="form-control" list="presetColors" value="#ffffff">
                                        <datalist id="presetColors">
                                            @foreach($colors as $color)
                                                <option>{{$color->hex}}</option>
                                            @endforeach
                                        </datalist>
                                    </div>
                                    <div class="col-lg-2">
                                        <label for="settings[`+new_id+`][max_width]">@lang('Bredd')</label>
                                        <input name="settings[`+new_id+`][max_width]" class="form-control">
                                    </div>
                                    <div class="col-lg-2">
                                        <label for="settings[`+new_id+`][max_height]">@lang('Höjd')</label>
                                        <input name="settings[`+new_id+`][max_height]" class="form-control">
                                    </div>
                                    <div class="col-lg-3">
                                        <label for="settings[`+new_id+`][adjustment]">@lang('Justering')</label>
                                        <select class="custom-select d-block w-100" name="settings[`+new_id+`][adjustment]">
                                            <option value="float-left">@lang('Vänster')</option>
                                            <option value="mx-auto">@lang('Centrerad')</option>
                                            <option value="float-right">@lang('Höger')</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <br>

                            <label class="handle" for="new_flipcard_front[`+new_id+`]">
                                @lang('Text framsida')
                            </label>
                            <a href="#" class="close remove_field" data-dismiss="alert" aria-label="close">&times;</a>
                            <textarea rows="4" name="new_flipcard_front[`+new_id+`]" class="form-control twe original-content"></textarea>
                            <label class="handle" for="new_flipcard_back[`+new_id+`]">
                                @lang('Text baksida')
                            </label>
                            <textarea rows="4" name="new_flipcard_back[`+new_id+`]" class="form-control twe original-content"></textarea>
                        </div>
                    </div>
                `);
                addtwe();
                break;
            case 'file':
                $(wrapper).append(`
                <div id="new_file[`+new_id+`]" data-id="`+new_id+`" class="card">
                    <div class="card-header">
                        <span class="handle"><i class="fas fa-arrows-alt-v"></i></span>
                        <label class="handle" for="new_file[`+new_id+`]">@lang('Övrig fil')</label>
                        <a href="#" class="close remove_field pl-3" data-dismiss="alert" aria-label="close">&times;</a>
                        <a data-toggle="collapse" href="#body`+new_id+`" id="collapstoggle`+new_id+`">
                            <i class="fas fa-chevron-up float-right text-dark"></i>
                        </a>
                    </div>
                    <div class="collapse multi-collapse show" id="body`+new_id+`">
                        <div class="card-body">
                            <input name="new_file[`+new_id+`]" class="form-control original-content" type="file">
                        </div>
                    </div>
                </div>
                `);
                break;
            case 'pagebreak':
                $(wrapper).append(`
                <div id="new_pagebreak[`+new_id+`]" data-id="`+new_id+`" class="card">
                    <div class="card-header">
                        <span class="handle"><i class="fas fa-arrows-alt-v"></i></span>
                        <label class="handle" for="new_pagebreak[`+new_id+`]">@lang('Sidrubrik')</label>
                        <a href="#" class="close remove_field pl-3" data-dismiss="alert" aria-label="close">&times;</a>
                        <a data-toggle="collapse" href="#body`+new_id+`" id="collapstoggle`+new_id+`">
                            <i class="fas fa-chevron-up float-right text-dark"></i>
                        </a>
                    </div>
                    <div class="collapse multi-collapse show" id="body`+new_id+`">
                        <div class="card-body">
                            <div class="container">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <input name="new_pagebreak[`+new_id+`]" class="form-control original-content">
                                    </div>
                                    <div class="col-lg-2">
                                        <label for="content_colors[`+new_id+`]">@lang('Färg')</label>
                                        <input name="content_colors[`+new_id+`]" type="color" list="presetColors">
                                        <datalist id="presetColors">
                                            @foreach($colors as $color)
                                                <option>{{$color->hex}}</option>
                                            @endforeach
                                        </datalist>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                `);
                break;
            case 'toc':
                $(wrapper).append(`
                <div id="new_toc[`+new_id+`]" data-id="`+new_id+`" class="card">
                    <div class="card-header">
                        <span class="handle"><i class="fas fa-arrows-alt-v"></i></span>
                        <label class="handle" for="new_toc[`+new_id+`]">@lang('Innehållsförteckning')</label>
                        <a href="#" class="close remove_field pl-3" data-dismiss="alert" aria-label="close">&times;</a>
                        <a data-toggle="collapse" href="#body`+new_id+`" id="collapstoggle`+new_id+`">
                            <i class="fas fa-chevron-up float-right text-dark"></i>
                        </a>
                    </div>
                    <div class="collapse multi-collapse show" id="body`+new_id+`">
                        <div class="card-body">
                            <input hidden name="new_toc[`+new_id+`]" class="form-control original-content">
                        </div>
                    </div>
                </div>
                `);
                break;
        }
        document.lesson.submit.disabled = false;
        update_content_order();
        $("#content_to_add").val('select');
    });

    $(wrapper).on("click",".remove_field", function(e){
        var confirmquestion;
        if($(this).data("translations") > 1) {
            confirmquestion = '@lang('Vill du verkligen radera detta innehåll inklusive översättningar?')';
        } else {
            confirmquestion = '@lang('Vill du verkligen radera detta innehåll?')';
        }
        if(confirm(confirmquestion)) {
            e.preventDefault();
            var parentdiv = $(this).parent('div').parent('div');
            var textbox = $(this).parent('div').parent('div').find('.original-content');
            var oldname = textbox.attr('name');
            var id = oldname.substring(oldname.lastIndexOf("["), oldname.lastIndexOf("]")+1);
            parentdiv.hide();
            textbox.attr('name', 'remove_content'+id);
        }
    });

});

</script>
