<div id="youtube[{{$content->id}}]" data-id="{{$content->id}}" class="card">
    <div class="card-header">
        <span class="handle"><i class="fas fa-arrows-alt-v"></i></span>
        <label class="handle" for="youtube[{{$content->id}}]">
            @lang('Youtube-film') - {{$content->summary}}
        </label>
        @if(locale_is_default())
            <a href="#" class="close remove_field pl-3" data-dismiss="alert" data-translations="{{$content->translations()->count()}}" aria-label="close">&times;</a>
        @endif
        <a data-toggle="collapse" href="#body{{$content->id}}" id="collapstoggle{{$content->id}}">
            <i class="fas fa-chevron-up float-right text-dark"></i>
        </a>
    </div>
    <div class="collapse multi-collapse show" id="body{{$content->id}}">
        <div class="card-body">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4">
                        <label for="youtube[{{$content->id}}]">@lang('ID')</label>
                        <input name="youtube[{{$content->id}}]" class="form-control original-content" value="{{$content->content}}" maxlength="11">
                    </div>
                    <div class="col-lg-2">
                        <label for="settings[{{$content->id}}][max_width]">@lang('Maxbredd')</label>
                        <input name="settings[{{$content->id}}][max_width]" class="form-control" value="{{$content->max_width}}">
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
        </div>
    </div>
</div>