<div id="flipcard[{{$content->id}}]" data-id="{{$content->id}}" class="card">
    <div class="card-header">
        <span class="handle"><i class="fas fa-arrows-alt-v"></i></span>
        <label class="handle">@lang('Vändkort')</label>
        @if(locale_is_default())
            <a href="#" class="close remove_field" data-dismiss="alert" data-translations="{{$content->translations()->count()}}" aria-label="close">&times;</a>
        @endif
    </div>
    <div class="card-body">
        <div class="container">
            <div class="row">
                <div class="col-lg-2">
                    <label for="content_colors[{{$content->id}}]">@lang('Färg')</label>
                    <input name="content_colors[{{$content->id}}]" style="height:35px" type="color" class="form-control" list="presetColors" value="{{$content->color->hex}}">
                    <datalist id="presetColors">
                        @foreach($colors as $color)
                            <option>{{$color->hex}}</option>
                        @endforeach
                    </datalist>
                </div>
                <div class="col-lg-2">
                    <label for="settings[{{$content->id}}][max_width]">@lang('Bredd')</label>
                    <input name="settings[{{$content->id}}][max_width]" class="form-control" value="{{$content->max_width}}">
                </div>
                <div class="col-lg-2">
                    <label for="settings[{{$content->id}}][max_height]">@lang('Höjd')</label>
                    <input name="settings[{{$content->id}}][max_height]" class="form-control" value="{{$content->max_height}}">
                </div>
                <div class="col-lg-3">
                    <label for="settings[{{$content->id}}][adjustment]">@lang('Justering')</label>
                    <select class="custom-select d-block w-100" name="settings[{{$content->id}}][adjustment]">
                        <option {{$content->adjustment=='float-left'||old("adjustment[".$content->id."]")=='float-left'?"selected":""}} value="float-left">@lang('Vänster')</option>
                        <option {{$content->adjustment=='mx-auto'||old("adjustment[".$content->id."]")=='mx-auto'?"selected":""}} value="mx-auto">@lang('Centrerad')</option>
                        <option {{$content->adjustment=='float-right'||old("adjustment[".$content->id."]")=='float-right'?"selected":""}} value="float-right">@lang('Höger')</option>
                    </select>
                </div>
            </div>
        </div>

        <br>


        <label class="handle" for="flipcard_front[{{$content->id}}]">
            @lang('Text framsida')
            @if(!$content->hasTranslation(\App::getLocale()))
                (@lang('Översatt innehåll saknas - visar innehåll från standardspråk'))
            @endif
        </label>
        <textarea rows="4" name="flipcard_front[{{$content->id}}]" class="form-control twe original-content">{!!$content->textPart(0)!!}</textarea>

        <label class="handle" for="flipcard_back[{{$content->id}}]">
            @lang('Text baksida')
        </label>
        <textarea rows="4" name="flipcard_back[{{$content->id}}]" class="form-control twe original-content">{!!$content->textPart(1)!!}</textarea>

    </div>
</div>