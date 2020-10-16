<div id="vimeo[{{$content->id}}]" data-id="{{$content->id}}" class="card">
    <div class="card-body">
        <span class="handle"><i class="fas fa-arrows-alt-v"></i></span>
        <label class="handle" for="vimeo[{{$content->id}}]">@lang('Vimeo-film')</label>
        @if(locale_is_default())
            <a href="#" class="close remove_field" data-dismiss="alert" aria-label="close">&times;</a>
        @endif
        <div class="container">
            <div class="row">
                <div class="col-lg-4">
                    <label for="vimeo[{{$content->id}}]">@lang('ID')</label>
                    <input name="vimeo[{{$content->id}}]" class="form-control original-content" value="{{$content->content}}">
                </div>
                <div class="col-lg-2">
                    <label for="settings[{{$content->id}}][max_width]">@lang('Maxbredd')</label>
                    <input name="settings[{{$content->id}}][max_width]" class="form-control" value="{{$content->max_width}}">
                </div>
                <div class="col-lg-3">
                    <label for="settings[{{$content->id}}][adjustment]">@lang('Justering')</label>
                    <select class="custom-select d-block w-100" name="settings[{{$content->id}}][adjustment]">
                        <option {{$content->adjustment=='left'||old("adjustment[".$content->id."]")=='left'?"selected":""}} value="left">@lang('Vänster')</option>
                        <option {{$content->adjustment=='center'||old("adjustment[".$content->id."]")=='center'?"selected":""}} value="center">@lang('Centrerad')</option>
                        <option {{$content->adjustment=='right'||old("adjustment[".$content->id."]")=='right'?"selected":""}} value="right">@lang('Höger')</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>